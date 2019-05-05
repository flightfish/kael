<?php
namespace console\controllers;

use common\libs\EmailApi;
use common\libs\PinYin;
use common\models\CommonUser;
use common\models\DingtalkUser;
use Yii;
use yii\console\Controller;


class EmailController extends Controller
{

    public function actionInit(){
        /**
        alter table `dingtalk_user`
        add `email_pinyin` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱拼音' AFTER `email`,
        add `email_suffix` varchar(255) NOT NULL DEFAULT '' COMMENT '后缀，用于邮箱创建' after `email_pinyin`,
        add `email_number` int(11) NOT NULL DEFAULT '0' COMMENT '序号，用于邮箱创建' AFTER `email_suffix`,
        add `email_errno` int(11) NOT NULL DEFAULT '-1' COMMENT '错误类型 0正常 1多音字 2姓名中包含非汉字字符 3名字长度过长(大于10位) 4名字长度过短(小于2位)'  AFTER `email_number`,
        add `email_errmsg` varchar(255) NOT NULL DEFAULT '' COMMENT '错误详情' AFTER `email_errno`,
        add `email_created` tinyint(4) NOT NULL DEFAULT '0' COMMENT '邮箱创建状态 0创建中 1已创建 2创建异常 3注销中 4已注销' AFTER `email_errmsg`,
        add INDEX idx_pinyin_emailsuffix_emailnumber(`email_pinyin`,`email_suffix`,`email_number`);
         */
        $allList = DingtalkUser::find()->where(['!=','email',''])->andWhere(['email_pinyin'=>''])
            ->asArray(true)->all();
        foreach ($allList as $v){
            $match = [];
            preg_match_all('/^([a-zA-z]+)([0-9]*)(-intern)*@knowbox.cn$/',$v['email'],$match);
            if(empty($match[1])){
                DingtalkUser::updateAll(
                    [
                        'email_errno'=>5,
                        'email_errmsg'=>'钉钉邮箱格式错误'
                    ],
                    ['user_id'=>$v['user_id']]);
                continue;
            }
            $pinyin = $match[1][0];
            $number = $match[2][0];
            $suffix = $match[3][0];
            DingtalkUser::updateAll(
                [
                    'email_pinyin'=>$pinyin,
                    'email_suffix'=>$suffix,
                    'email_number'=>intval($number),
                    'email_errno'=>0
                ],
                ['user_id'=>$v['user_id']]);
        }
    }

    public function actionGenPinyin(){
        if(exec('ps -ef|grep "email/gen-pinyin"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo date("Y-m-d H:i:s")." is_running\n";
            exit();
        }
        $list = DingtalkUser::find()
            ->where([
                'status'=>0,
                'email_errno'=>0,
                'email'=>'',
                'email_pinyin'=>''
            ])
            ->asArray(true)
            ->all();
        foreach ($list as $v){
            $v['name'] = trim($v['name']);
            if (preg_match( "/^[\x{4e00}-\x{9fa5}]+$/u", $v['name'])) {
                //全中文
                $len = mb_strlen($v['name']);
                if($len > 10){
                    DingtalkUser::updateAll(
                        ['email_errno'=>3, 'email_errmsg'=>"姓名长度过长{$len}"],
                        ['user_id'=>$v['user_id']]);
                    continue;
                }
                if($len < 2){
                    DingtalkUser::updateAll(
                        ['email_errno'=>4, 'email_errmsg'=>"姓名长度过短{$len}"],
                        ['user_id'=>$v['user_id']]);
                    continue;
                }

                $error = 0;
                $pinyinArr = PinYin::getDuoyin($v['name']);
                $pinyin = '';
                $i = 0;
                foreach ($pinyinArr as $zi=>$pinyinOne){
                    if(empty($pinyinOne)){
                        DingtalkUser::updateAll(
                            [
                                'email_errno'=>5,
                                'email_errmsg'=>"存在生僻字-{$zi}"
                            ],
                            ['user_id'=>$v['user_id']]);
                        $error = 1;
                        break;
                    }
                    if(count($pinyinOne) > 1 && ($i==0 || $len = 2)){
                        DingtalkUser::updateAll(
                            [
                                'email_errno'=>1,
                                'email_errmsg'=>"存在多音字-{$zi}-".join(',',$pinyinOne)
                            ],
                            ['user_id'=>$v['user_id']]);
                        $error = 1;
                        break;
                    }elseif(count($pinyinOne) > 1){
                        $pinyinOne = array_values(array_unique(array_map(function($v){return $v[0];},$pinyinOne)));
                        if(count($pinyinOne) > 1){
                            DingtalkUser::updateAll(
                                [
                                    'email_errno'=>1,
                                    'email_errmsg'=>"存在多音字-{$zi}-".join(',',$pinyinOne)
                                ],
                                ['user_id'=>$v['user_id']]);
                            $error = 1;
                            break;
                        }
                    }

                    $pinyin .= $pinyinOne[0];
                    $i++;
                }
                if($error){
                    continue;
                }
                $maxNumOne = DingtalkUser::find()
                    ->where(['email_pinyin'=>$pinyin,'email_suffix'=>$v['email_suffix']])
                    ->orderBy('email_number desc')->limit(1)->asArray(true)->one();
                if(empty($maxNumOne)){
                    $number = 0;
                }else{
                    $number = $maxNumOne['email_number'] + 1;
                }
                DingtalkUser::updateAll(
                    [
                        'email_pinyin'=>$pinyin,
                        'email_number'=>intval($number),
                        'email_errno'=>0
                    ],
                    ['user_id'=>$v['user_id']]);
            }else{
                DingtalkUser::updateAll(
                    [
                        'email_errno'=>2,
                        'email_errmsg'=>'姓名中包含非汉字字符'
                    ],
                    ['user_id'=>$v['user_id']]);
                continue;
            }
        }
    }

    public function actionGenEmail(){
        //生成email
        if(exec('ps -ef|grep "email/gen-email"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo date("Y-m-d H:i:s")." is_running\n";
            exit();
        }
        $list = DingtalkUser::find()
            ->where([
                'status'=>0,
                'email_errno'=>0,
                'email'=>''
            ])
            ->andWhere(['!=','email_pinyin',''])
            ->asArray(true)
            ->all();
        foreach ($list as $v){
            if(empty($v['email_number'])){
                $email = $v['email_pinyin'].$v['email_suffix'].'@knowbox.cn';
            }else{
                $email = $v['email_pinyin'].$v['email_number'].$v['email_suffix'].'@knowbox.cn';
            }
            DingtalkUser::updateAll(['email'=>$email],['user_id'=>$v['user_id']]);
        }
    }

    public function actionCreateDetailEmail(){
        //删除
        $listForDel = DingtalkUser::find()
            ->select('user_id,email')
            ->where(['email_created'=>[1,3],'status'=>1]) //已创建 注销中
            ->andWhere(['like','email','@knowbox.cn'])
            ->asArray(true)
            ->all();
        if(!empty($listForDel)){
            $emailForDelAll = array_column($listForDel,'email');
            $emailToId = array_column($listForDel,'user_id','email');
            $emailForDelChunk = array_chunk($emailForDelAll,10);
            foreach ($emailForDelChunk as $emailForDel){
                $checkList = EmailApi::batchCheck($emailForDel);
                if(!empty($checkList['list'])){
                    foreach ($checkList['list'] as $v){
                        if($v['type'] == -1){
                            continue;
                        }
                        if($v['type'] == 1){
                            if(Yii::$app->params['env'] != 'prod'){
                                if(strpos($v['user'],'emailtest') === false){
                                    DingtalkUser::updateAll(['email_created'=>4],['user_id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //查询还有没有其他账号在用
                            $others = DingtalkUser::find()->where(['status'=>0,'email'=>$v['user']])
                                ->asArray(true)->limit(1)->one();
                            if(empty($others)){
                                //没有有效账号则删除
                                echo 'del - '.$v['user']."\n";
                                echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                                EmailApi::deleteUser($v['user']);
                            }
                        }
                        DingtalkUser::updateAll(['email_created'=>4],['user_id'=>$emailToId[$v['user']]]);
                    }
                }
            }
        }

        //创建
        $listUpdate = DingtalkUser::find()
            ->select('user_id,name,email')
            ->where(['email_created'=>[0,4],'status'=>0]) //创建中 已注销
            ->andWhere(['like','email','@knowbox.cn'])
            ->asArray(true)
            ->all();
        if(!empty($listUpdate)){
            $emailForUpdateAll = array_column($listUpdate,'email');
            $emailToName = array_column($listUpdate,'name','email');
            $emailToId = array_column($listUpdate,'user_id','email');
            $emailForUpdateChunk = array_chunk($emailForUpdateAll,10);
            foreach ($emailForUpdateChunk as $emailForUpdate){
                $checkList = EmailApi::batchCheck($emailForUpdate);
                if(!empty($checkList['list'])){
                    foreach ($checkList['list'] as $v){
                        if($v['type'] == -1){
                            continue;
                        }
                        if($v['type'] == 0){
                            if(Yii::$app->params['env'] != 'prod'){
                                if(strpos($v['user'],'emailtest') === false){
                                    try{
                                        $emailToId[$v['user']];
                                    }catch (\Exception $e){
                                        var_dump($v);
                                        throw $e;
                                    }
                                    DingtalkUser::updateAll(['email_created'=>1],['user_id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //添加
                            echo 'add - '. $v['user']."\n";
                            echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                            try{
                                EmailApi::addUser($v['user'],$emailToName[$v['user']],'1Knowbox!');
                            }catch (\Exception $e){
                                echo $e->getMessage()."\n";
                                continue;
                            }
                        }
                        DingtalkUser::updateAll(['email_created'=>1],['user_id'=>$emailToId[$v['user']]]);
                    }
                }
            }

        }


        //删除
        //查询成员
        $allUsers = EmailApi::getDepartmentListUser();
        $allUsers = $allUsers['userlist'];
        $allUsers = array_column($allUsers,'userid');
        //所有有效的
        $allUserEmail = DingtalkUser::find()
            ->select('email')
            ->where(['status'=>0])
            ->andWhere(['like','email','knowbox.cn'])
            ->asArray()->column();
        $allUserEmail = array_map('trim',$allUserEmail);
        //无效成员
        $inValidList = array_diff($allUsers,$allUserEmail);
        foreach ($inValidList as $v){
            echo "invalid email:".$v."\n";
            EmailApi::deleteUser($v);
        }
    }


    /**
     * 初始化用户信息到EMAIL
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "email/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $listForDel = CommonUser::getDb()->createCommand("select id,username,email from `user` where user_type = 0 and email_created = 1 and email != '' and status!=0")->queryAll();
        $listUpdate = CommonUser::getDb()->createCommand("select id,username,email from `user` where user_type = 0 and email_created = 0 and email != '' and status=0")->queryAll();


        /**
        {
        "errcode": 0,
        "errmsg": "ok",
        "list": [
        {"user":"zhangsan@bjdev.com", "type":1}, 帐号类型。-1:帐号号无效; 0:帐号名未被占用; 1:主帐号; 2:别名帐号; 3:邮件群组帐号
        {"user":"zhangsangroup@shdev.com", "type":3}
        ]
        }
         */
        if(!empty($listForDel)){
            $emailForDelAll = array_column($listForDel,'email');
            $emailToId = array_column($listForDel,'id','email');
            $emailForDelChunk = array_chunk($emailForDelAll,10);
            foreach ($emailForDelChunk as $emailForDel){
                $checkList = EmailApi::batchCheck($emailForDel);
                if(!empty($checkList['list'])){
                    foreach ($checkList['list'] as $v){
                        if($v['type'] == -1){
                            continue;
                        }
                        if($v['type'] == 1){
                            if(Yii::$app->params['env'] != 'prod'){
                                if(strpos($v['user'],'emailtest') === false){
                                    CommonUser::updateAll(['email_created'=>0],['id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //查询还有没有其他账号在用
                            $others = CommonUser::find()->where(['status'=>0,'email'=>$v['user'],'user_type'=>0])
                                ->asArray(true)->limit(1)->one();
                            if(empty($others)){
                                //没有有效账号则删除
                                echo 'del - '.$v['user']."\n";
                                echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                                EmailApi::deleteUser($v['user']);
                            }
                        }
                        CommonUser::updateAll(['email_created'=>0],['id'=>$emailToId[$v['user']]]);
                    }
                }
            }

        }
        if(!empty($listUpdate)){
            $emailForUpdateAll = array_column($listUpdate,'email');
            $emailToName = array_column($listUpdate,'username','email');
            $emailToId = array_column($listUpdate,'id','email');
            $emailForUpdateChunk = array_chunk($emailForUpdateAll,10);
            foreach ($emailForUpdateChunk as $emailForUpdate){
                $checkList = EmailApi::batchCheck($emailForUpdate);
                if(!empty($checkList['list'])){
                    foreach ($checkList['list'] as $v){
                        if($v['type'] == -1){
                            continue;
                        }
                        if($v['type'] == 0){
                            if(Yii::$app->params['env'] != 'prod'){
                                if(strpos($v['user'],'emailtest') === false){
                                    try{
                                        $emailToId[$v['user']];
                                    }catch (\Exception $e){
                                        var_dump($v);
                                        throw $e;
                                    }
                                    CommonUser::updateAll(['email_created'=>1],['id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //添加
                            echo 'add - '. $v['user']."\n";
                            echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                            try{
                                EmailApi::addUser($v['user'],$emailToName[$v['user']],'Know11');
                            }catch (\Exception $e){
                                echo $e->getMessage()."\n";
                                continue;
                            }
                        }
                        CommonUser::updateAll(['email_created'=>1],['id'=>$emailToId[$v['user']]]);
                    }
                }
            }

        }

        //查询成员
        $allUsers = EmailApi::getDepartmentListUser();
        $allUsers = $allUsers['userlist'];
        $allUsers = array_column($allUsers,'userid');
        //所有有效的
        $allUserEmail = CommonUser::find()
            ->select('email')
            ->where(['status'=>0,'user_type'=>0])
            ->andWhere(['like','email','knowbox.cn'])
            ->asArray()->column();
        $allUserEmail = array_map('trim',$allUserEmail);
        //无效成员
        $inValidList = array_diff($allUsers,$allUserEmail);
        foreach ($inValidList as $v){
            echo "invalid email:".$v."\n";
            EmailApi::deleteUser($v);
        }
    }
}
