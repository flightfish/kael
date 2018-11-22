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
        $ds = ldap_connect(Yii::$app->params['ldap_addr'],389) or die("Could not connect to LDAP server.");
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        try{
            ldap_bind($ds, Yii::$app->params['ldap_rdn'], Yii::$app->params['ldap_passwd']);
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
                $dn = "mobile={$v['mobile']},ou={$ou},dc=kb,dc=com";
//                $dn = "cn=test10,ou=People,dc=kb,dc=com";
                //查询旧的
                $sr= ldap_search($ds, "dc=kb,dc=com", "(uidNumber={$v['id']})", ["ou", "uidNumber"]);
                $old = ldap_get_entries($ds, $sr);
                $needAdd = 0;
                $dnOld = "";
                if($old['count'] == 0){
                    //空的
                    $needAdd = 1;
                }else{
                    $dnOld = $old[0]['dn'];
                    if($dnOld != $dn){
                        $ret = ldap_delete($ds,$dnOld);
                        echo "delold {$dnOld} - " . intval($ret)."\n";
                        if($v['status'] == 0){
                            $needAdd = 1;
                        }
                    }elseif($v['status'] !=0){
                        //删除
                        $ret = ldap_delete($ds,$dn);
                        $ret && CommonUser::updateAll(['ldap_update_time'=>date('Y-m-d H:i:s')],['id'=>$v['id']]);
                        echo "del {$dn} - " . intval($ret)."\n";
                        continue;
                    }
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
                    'departmentNumber'=>$v['department_id'],
                    'employeeNumber'=>$v['work_number'],
                ];
                if($needAdd){
                    $addInfo['objectclass'] = ["inetOrgPerson","organizationalPerson","person"];
                    echo $dn ."-" .json_encode(array_filter($addInfo),JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
                    $ret = ldap_add($ds, $dn, array_filter($addInfo));
                    echo "add {$dn} - " . intval($ret)."\n";
                    $ret && CommonUser::updateAll(['ldap_update_time'=>date('Y-m-d H:i:s')],['id'=>$v['id']]);
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
