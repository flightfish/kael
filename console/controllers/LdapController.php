<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\models\CommonUser;
use Yii;
use yii\console\Controller;


class LdapController extends Controller
{
    /**
     * 初始化用户信息到LDAP
     */
    public function actionInit(){
        $ds = ldap_connect("10.9.58.21",389) or die("Could not connect to LDAP server.");
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        try{
            ldap_bind($ds, "cn=Manager,dc=kb,dc=com", "ldap123_dc");
            $list = CommonUser::getDb()->createCommand("select * from `user` where ldap_update_time < update_time")->queryAll();
            if(empty($list)){
                return false;
            }
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


            foreach ($list as $v){
                $ou = $v['user_type'] == 0 ? 'employee' : 'contractor';
                $passwd = '{MD5}'.base64_encode(pack("H*",md5($v['password'])));
                $dn = "uidNumber={$v['id']},ou={$ou},dc=kb,dc=com";
                if($v['status'] !=0 ){
                    //删除
                    $ret = ldap_delete($ds,$dn);
                    $ret && CommonUser::updateAll(['ldap_update_time'=>date('Y-m-d H:i:s')],['id'=>$v['id']]);
                    echo "del {$dn} - " . intval($ret)."\n";
                    continue;
                }
                $addInfo = [
//                    'uidNumber'=>$v['id'],
//                    'uid'=>$v['username'],
//                    'cn'=>$v['username'],
//                    'sn'=>$v['username'],
                    'ou'=>$ou,
//                    'userPassword'=>$passwd,
//                    'employeeType'=>$v['user_type'],
//                    'mobile'=>$v['mobile'],
//                    'mail'=>$v['email'],
//                    'departmentNumber'=>$v['department_id'],
//                    'employeeNumber'=>$v['work_number'],
                ];
                if($v['ldap_update_time'] == '0000-00-00 00:00:00'){
                    echo $dn ."-" .json_encode(array_filter($addInfo),JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
                    $ret = ldap_add($ds, $dn, array_filter($addInfo));
                    echo "add {$dn} - " . intval($ret)."\n";
                    $ret && CommonUser::updateAll(['ldap_update_time'=>date('Y-m-d H:i:s')],['id'=>$v['id']]);
                    exit();
                }else{
                    $ret = ldap_mod_replace($ds, $dn, $addInfo);
                    echo "mod {$dn} - " . intval($ret)."\n";
                }
            }
        }catch (\Exception $e){
            ldap_close($ds);
            throw $e;
        }

    }
}
