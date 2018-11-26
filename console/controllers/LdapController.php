<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\models\CommonUser;
use common\models\Department;
use Yii;
use yii\console\Controller;


class LdapController extends Controller
{
    /**
     * 初始化用户信息到LDAP
     */
    public function actionUpdate(){
        if(empty(Yii::$app->params['ldap_addr'])){
            echo "未设置ldap地址\n";exit();
        }
        echo $caPath = dirname(dirname(dirname(__FILE__))).'/common/config/ca.crt';
        putenv('LDAPTLS_CACERT='.$caPath);
        echo Yii::$app->params['ldap_addr'].':'.Yii::$app->params['ldap_port']."\n";
        $ds = ldap_connect(Yii::$app->params['ldap_addr'],Yii::$app->params['ldap_port']) or die("Could not connect to LDAP server.");
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        try{
            ldap_bind($ds, Yii::$app->params['ldap_rdn'], Yii::$app->params['ldap_passwd']);
            //先查看删除的
            $listOld = CommonUser::getDb()->createCommand("select * from `user` where ldap_update_time < update_time and status!=0")->queryAll();
            $listUpdate = CommonUser::getDb()->createCommand("select * from `user` where ldap_update_time < update_time and status=0")->queryAll();
            $departmentNameIndex = array_column(Department::findAllList(),'department_name','department_id');
//            $idList = array_column($list,'id');
//            $filterSub = join('',array_map(function($id){return "(uidNumber={$id})";},$idList));
//            $sr = "dc=kb,dc=com";
//            $filter="(|$filterSub)";
//            $filter="(|(uidNumber=20006)(uidNumber=20005))";
//            $justthese = array("ou", "uidNumber");
//            $sr=ldap_search($ds, $sr, $filter, $justthese);
//            $oldList = ldap_get_entries($ds, $sr);
//            unset($oldList['count']);
//            $delDnList = array_filter(array_column($oldList,'dn'));

            foreach ($listOld as $v){
                $v['mobile'] = trim($v['mobile']);
                $mobileMatch = [];
                preg_match('/\d{11}/',$v['mobile'],$mobileMatch);
                if(empty($mobileMatch)){
                    continue;
                }
                $v['mobile'] = $mobileMatch[0];
                //查询旧的
                $sr= ldap_search($ds, "dc=kb,dc=com", "(|(uid={$v['id']})(mobile={$v['mobile']}))", ["ou", "uid"]);
                $old = ldap_get_entries($ds, $sr);
                if($old['count'] > 0){
                    $dnOld = $old[0]['dn'];
                    //删除
                    $ret = ldap_delete($ds,$dnOld);
                    $ret && CommonUser::updateAll(['ldap_update_time'=>date('Y-m-d H:i:s')],['id'=>$v['id']]);
                    echo "del {$dnOld} - " . intval($ret)."\n";
                    continue;
                }
            }
            //更新
            foreach ($listUpdate as $v){
                $v['mobile'] = trim($v['mobile']);
                $mobileMatch = [];
                preg_match('/\d{11}/',$v['mobile'],$mobileMatch);
                if(empty($mobileMatch)){
                    continue;
                }
                $v['mobile'] = $mobileMatch[0];
                $ou = $v['user_type'] == 0 ? 'Employee' : 'Contractor';
//                $passwd = '{MD5}'.base64_encode(pack("H*",md5($v['password'])));
                $passwd = '{MD5}'.base64_encode(pack("H*",$v['password']));
                $dn = "mobile={$v['mobile']},ou={$ou},dc=kb,dc=com";
                //查询旧的
                var_dump("(!(uid={$v['id']})(mobile={$v['mobile']}))");
                $sr= ldap_search($ds, "dc=kb,dc=com", "(|(uid={$v['id']})(mobile={$v['mobile']}))", ["ou", "uid"]);
                $old = ldap_get_entries($ds, $sr);
                if($old['count'] > 0){
                    $dnOld = $old[0]['dn'];
                    $ret = ldap_delete($ds,$dnOld);
                    echo "delold {$dnOld} - " . intval($ret)."\n";
                    $needAdd = 1;
                }
                $addInfo = [
                    'uid'=>$v['id'],
                    'cn'=>$v['username'],
                    'sn'=>$v['username'],
                    'ou'=>$ou,
                    'userPassword'=>$passwd,
                    'employeeType'=>$v['user_type'],
                    'mobile'=>$v['mobile'],
                    'mail'=>$v['email'],
                    'departmentNumber'=>$departmentNameIndex[$v['department_id']] ?? "未知部门",
                    'employeeNumber'=>$v['work_number'],
                ];
                $addInfo['objectclass'] = ["inetOrgPerson","organizationalPerson","person"];
                echo $dn ."-" .json_encode(array_filter($addInfo),JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
                $ret = ldap_add($ds, $dn, array_filter($addInfo));
                echo "add {$dn} - " . intval($ret)."\n";
                $ret && CommonUser::updateAll(['ldap_update_time'=>date('Y-m-d H:i:s')],['id'=>$v['id']]);
            }
        }catch (\Exception $e){
            ldap_close($ds);
            throw $e;
        }

    }
}
