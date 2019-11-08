<?php
namespace console\controllers;

use common\models\CommonUser;
use common\models\DingtalkUser;
use common\models\ehr\TrCandidateForSchool;
use Yii;
use yii\console\Controller;


class SendEmailController extends Controller
{

    public function actionTrSchoolAllExport(){
        $emailList = [
            'hujie@knowbox.cn',
            'wangjing2@knowbox.cn',
//            'houlk@knowbox.cn',
            'luyl@knowbox.cn',
            'wangchao@knowbox.cn',
            'liqiang@knowbox.cn'
        ];
        $select = 'mobile, email, name, sex, join_city_name, city_name, school_name, grade, subject_1, subject_2, subject_3, job_type, create_time';
        $list = TrCandidateForSchool::findList([],$select);
        $data = [
            [
                '姓名',
                '手机号',
                '邮箱',
                '性别',
                '入职城市',
                '所在城市',
                '学校',
                '年级',
                '第一志愿',
                '第二志愿',
                '第三志愿',
                '职种',
                '报名时间'
            ]
        ];
        foreach ($list as $v){
            $data[] = [
                $v['name'],
                '\''.$v['mobile'],
                $v['email'],
                [1=>'男',2=>'女'][$v['sex']]??'未知',
                $v['join_city_name'],
                $v['city_name'],
                $v['school_name'],
                [101=>'大一',102=>'大二',103=>'大三',104=>'大四'][$v['grade']]??'未知',
                [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_1']]??'未知',
                [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_2']]??'未知',
                [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_3']]??'未知',
                [1=>'兼职',2=>'全职'][$v['job_type']]??'未知',
                $v['create_time']
            ];
        }
        $filename = '/data/wwwroot/kael/console/runtime/'.microtime(true).'.xls';
        $objPHPExcel = new \PHPExcel();
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->setTitle('候选人名单');
        $objSheet->fromArray($data);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $objWriter->save($filename);
        foreach ($emailList as $emailAddr){
            $mail= \Yii::$app->mailer->compose();
            $mail->setTo($emailAddr)
                ->setFrom( ['mail_service@knowbox.cn'=>'基地邮件通知'])
                ->setSubject("候选人名单")
                ->setTextBody("候选人名单")
                ->attach($filename);
            $ret = $mail->send();
            var_dump($ret);
        }


    }


    public function actionTrSchoolExport(){
        $cityNameList = [
            '长春'=>'hujie@knowbox.cn',
            '西安'=>'wangjing2@knowbox.cn',
            '长沙'=>'houlk@knowbox.cn',
            '合肥'=>'luyl@knowbox.cn',
        ];
        foreach ($cityNameList as $cityName=>$emailAddr){
            $select = 'mobile, email, name, sex, join_city_name, city_name, school_name, grade, subject_1, subject_2, subject_3, job_type, create_time';
            $list = TrCandidateForSchool::findList(['join_city_name'=>$cityName],$select);
            $data = [
                [
                    '姓名',
                    '手机号',
                    '邮箱',
                    '性别',
                    '入职城市',
                    '所在城市',
                    '学校',
                    '年级',
                    '第一志愿',
                    '第二志愿',
                    '第三志愿',
                    '职种',
                    '报名时间'
                ]
            ];
            foreach ($list as $v){
                $data[] = [
                    $v['name'],
                    '\''.$v['mobile'],
                    $v['email'],
                    [1=>'男',2=>'女'][$v['sex']]??'未知',
                    $v['join_city_name'],
                    $v['city_name'],
                    $v['school_name'],
                    [101=>'大一',102=>'大二',103=>'大三',104=>'大四'][$v['grade']]??'未知',
                    [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_1']]??'未知',
                    [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_2']]??'未知',
                    [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_3']]??'未知',
                    [1=>'兼职',2=>'全职'][$v['job_type']]??'未知',
                    $v['create_time']
                ];
            }
            $filename = '/data/wwwroot/kael/console/runtime/'.microtime(true).'.xls';
            $objPHPExcel = new \PHPExcel();
            $objSheet = $objPHPExcel->getActiveSheet();
            $objSheet->setTitle('候选人名单');
            $objSheet->fromArray($data);
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
            $objWriter->save($filename);
            $mail= \Yii::$app->mailer->compose();
//            $emailAddr = 'wangchao@knowbox.cn';
            $mail->setTo($emailAddr)
                ->setFrom( ['mail_service@knowbox.cn'=>'基地邮件通知'])
                ->setSubject("候选人名单({$cityName})")
                ->setTextBody("候选人名单({$cityName})")
                ->attach($filename);
            $ret = $mail->send();
            var_dump($ret);
        }

    }


    public function actionBossClass(){
        $emailList = [
            'wangchao@knowbox.cn',
            'liyb1@knowbox.cn',
            'liqiang@knowbox.cn',

            'lijf@knowbox.cn',
            'liwj1@knowbox.cn',
            'liubo@knowbox.cn',
            'jingyx@knowbox.cn',
            'shaml@knowbox.cn',
            'liuwei3@knowbox.cn',
        ];
        $currentDay = date("Y-m-d");
        $currentTime = date("Y-m-d H:i:s");

        $sqlUserId = <<<SQL
        select distinct a.id
from user a
left join  relate_user_platform b on a.id = b.user_id
where a.status != 0 and a.user_type = 0 and b.user_id > 0;
SQL;
        $userIds = CommonUser::getDb()->createCommand($sqlUserId)->queryColumn();
        $userIdsStr = join(',',$userIds);

        $sqlMiniClass = <<<SQL
select a.number,a.title,count(distinct ms.user_number) as student_count,
       ci.start_time,ci.end_time,
       a.adviser_number,t.name as adviser_name,
       e.kael_id
from mini_class a
left join employee e on a.adviser_number = e.adviser_number -- and e.status = 1
left join employee e2 on a.adviser_number = e2.adviser_number and e2.status = 1
    and e2.kael_id not in ({$userIdsStr})

    left join teacher t on t.number = a.adviser_number
left join class_info ci on a.class_number = ci.number
left join mini_class_student ms on a.number = ms.mini_class_number  and ms.status = 1
left join course c on c.number=a.course_number
where ci.end_time > '2019-11-07' and ci.start_time > '2019-10-15' and a.adviser_number > 0 and ci.status != 4 and a.istest = 0
and a.title not like '%测试%' and a.title not like '%test%' and a.title not like '%预览%'
and c.category!=5
and e2.number is null
and e.kael_id in ($userIdsStr)
group by a.number
having student_count > 0
SQL;
        $miniClassList = Yii::$app->db_live->createCommand($sqlMiniClass)->queryAll();
        $count = count($miniClassList);
        if(empty($miniClassList)){
            //无
            foreach ($emailList as $emailAddr){
                $mail= \Yii::$app->mailer->compose();
                $mail->setTo($emailAddr)
                    ->setFrom( ['mail_service@knowbox.cn'=>'基地邮件通知'])
                    ->setSubject("{$currentDay}离职甩班通知")
                    ->setTextBody("截止{$currentTime}，未监控到甩班。");
                $ret = $mail->send();
                var_dump($ret);
            }
            echo "无小班未处理\n";
            exit();
        }
        $kaelIds = array_column($miniClassList,'kael_id');
        $kaelIdsStr = join(',',$kaelIds);

        $sql = <<<SQL
select a.kael_id,b.path_name from dingtalk_user a
left join dingtalk_department b on a.department_id = b.id
where a.kael_id in ($kaelIdsStr)
SQL;
        $kaelIdToDepartment = CommonUser::getDb()->createCommand($sql)->queryAll();
        $kaelIdToDepartmentIndex = [];
        foreach ($kaelIdToDepartment as $v){
            !empty($v['path_name']) && $kaelIdToDepartmentIndex[$v['kael_id']] = $v['path_name'];
        }

        $dataTitle = [
            '小班ID',
            '小班名称',
            '学生数',
            '开课时间',
            '结课时间',
            '辅导老师ID',
            '辅导老师名称',
            '一级部门',
            '二级部门',
            '三级部门',
            '四级部门',
            '五级部门',
            '六级部门'
        ];
        $data = [];
        foreach ($miniClassList as $v){
            $pathName = $kaelIdToDepartmentIndex[$v['kael_id']] ?? '';
            $pathNameArr = explode('/',$pathName);
            $data[] = [
                '\''.$v['number'],
                $v['title'],
                $v['student_count'],
                $v['start_time'],
                $v['end_time'],
                $v['adviser_number'],
                $v['adviser_name'],
                $pathNameArr[0]??'',
                $pathNameArr[1]??'',
                $pathNameArr[2]??'',
                $pathNameArr[3]??'',
                $pathNameArr[4]??'',
                $pathNameArr[5]??'',
            ];
        }

        usort($data,function ($a,$b){
            if($a[7] != $b[7]){
                return $a[7]<=>$b[7];
            }
            if($a[8] != $b[8]){
                return $a[8]<=>$b[8];
            }
            if($a[9] != $b[9]){
                return $a[9]<=>$b[9];
            }
            if($a[10] != $b[10]){
                return $a[10]<=>$b[10];
            }
            if($a[11] != $b[11]){
                return $a[11]<=>$b[11];
            }
            if($a[12] != $b[12]){
                return $a[12]<=>$b[12];
            }
            return 0;
        });
        array_unshift($data,$dataTitle);

        $filename = '/data/wwwroot/kael/console/runtime/miniclass'.microtime(true).'.xls';
        $objPHPExcel = new \PHPExcel();
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->setTitle("{$currentDay}离职甩班表");
        $objSheet->fromArray($data);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $objWriter->save($filename);
        foreach ($emailList as $emailAddr){
            $mail= \Yii::$app->mailer->compose();
            $mail->setTo($emailAddr)
                ->setFrom( ['mail_service@knowbox.cn'=>'基地邮件通知'])
                ->setSubject("{$currentDay}离职甩班通知")
                ->setTextBody("截止{$currentTime}有未处理甩班{$count}个，甩班列表见附件。")
                ->attach($filename,['fileName'=>"{$currentDay}离职甩班表.xls"]);
            $ret = $mail->send();
            var_dump($ret);
        }

    }

}
