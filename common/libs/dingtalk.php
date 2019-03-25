<?php
namespace   dzz\dingtalk\classes;
use \core as C;
use \DB as DB;
class Dingtalk{
    public function run( $arr=array() ){
        return;
    } 
    //钉钉端向dzz同步用户
    public static function dingSynuser( $arr=array() ){ 
        global $_G;
         if( $arr && $_G['setting']['Ding_status'] && $_G['setting']['Ding_CorpID'] && $_G['setting']['Ding_CorpSecret']){
            $data = array(
                'uid' => $arr['uid'], 
                'ding_userid' => $arr['userid'],
                'ding_unionid' => $arr['unionid'],
                'ding_openid' => $arr['openId'],
                'ding_id' => $arr['dingId'],
                'ding_remark' => $arr['remark'],
                'ding_email' => $arr['email'], 
                'ding_name' => $arr['name'],
                'ding_mobile' => $arr['mobile'],
                'update_time'=>TIMESTAMP,
                'ding_status' => intval($arr['active'])
            ); 
            $duser=C::tp_t("ding_user")->where("ding_userid='".$arr['userid']."'")->find();
            if( $duser){
                $did=$duser["id"];
                $duser=C::tp_t("ding_user")->where("id=".$did)->save($data);
            }else{
                $data['create_time']=TIMESTAMP;
                $did=C::tp_t("ding_user")->add( $data ); 
            } 
            return $did;
        } 
    }
    
    //dzz向钉钉端同步用户 机构端新增或者修改用户时调用
    public static function syntolineUser( $uids=array() , $action=null ,$force=false){ 
        global $_G;
		if(!$force && !$_G['setting']['Ding_auto_syn_up']) return ;
        if( $action=="del"){ return self::syntolineDeluser($uids);  }//删除与新增修改用的是用一个钩子
        $back=0;
        if($action=="returnjson"){
            $back=1;
        }
        $ret = 0;
        if( $uids && $_G['setting']['Ding_status'] && $_G['setting']['Ding_CorpID'] && $_G['setting']['Ding_CorpSecret']){
            $setting_ding = $_G['setting'];  
            $uids = (array)$uids;
            $ding = self::dingClass();
            if( $ding===false ){ 
                return $ret;
            }
            $i=0;
            foreach ($uids as $uid) {
                if (!$user = C::t('user')->fetch($uid)) continue;
                $worgids = array();
                $ding_orgids = array();
                if ($orgids = C::t('organization_user')->fetch_orgids_by_uid($uid)) { 
                    if ($orgids) {
                        $olist = C::t('organization')->fetch_all($orgids); 
                        foreach ($olist as $value) { 
                            if( $value['type']>0 ){//群主类型不同步至微信
                                continue;
                            }
                            
                            //查询是否已经绑定了钉钉部门
                            $ding_depart=DB::fetch_first("select * from " . DB::table('ding_organization') . " where  orgid=".$value["orgid"]);
                            if ($ding_depart ){//已经在本地绑定钉钉    
                                //查询钉钉端是否有该部门信息
                                $result_ding = $ding->getDept( $ding_depart["ding_deptment_id"] );
                                if($result_ding){
                                    $ding_orgids[] = $ding_depart['ding_deptment_id'];
                                }else{//本地数据库有钉钉数据，但钉钉端无
                                    $result_ding = self::syntolineDepartment( $value ); 
                                    if( $result_ding ) $ding_orgids[] = $result_ding;
                                }
                            }
                            else {//还未在本地绑定钉钉    一般不出现else 因上一步是同步部门
                                $result_ding = self::syntolineDepartment( $value ); 
                                if( $result_ding ) $ding_orgids[] = $result_ding; 
                            }  
                        }
                    }
                } 
                if( !$ding_orgids ) $ding_orgids=array(1);//默认同步到企业微信的跟部门下
                
                $profile = C::t('user_profile') -> fetch_all($user['uid']);
                $dinguser =$ding_local_user = array();
                $ding_local_user = DB::fetch_first("select * from " . DB::table('ding_user') . " where  uid=".$user['uid']); 
                if( $ding_local_user ){//本地已经绑定并且存在有效的钉钉用户
                    $dinguser = $ding->getUser( $ding_local_user["ding_userid"] );
                }
                
                if ( $dinguser && $ding_local_user ) {//本地钉钉表存在且钉钉端存在更新用户信息
                    $data = array(
                        "userid" => $ding_local_user["ding_userid"],
                        "name" => $user['username'],
                        "department"=>$ding_orgids,
                        //"position" => '',
                        "email" => $user['email']
                    );
                    if (array_diff($dinguser['department'], $ding_orgids)) {
                        $data['department'] = $ding_orgids;
                    }
                    if ($user['phone'] && $user['phone'] != $dinguser['mobile']) {
                        $data['mobile'] = $user['phone'];
                    }
        
                    if ($profile['telephone'] && $profile['telephone'] != $dinguser['telephone']) {
                        $data['tel'] = $profile['telephone'];
                    }
                    
                    if ($ding -> updateUser($data)) { 
                        $setarr['phone'] = empty($user['phone']) ? $dinguser['phone'] : $user['phone'];
                        $setarr['email'] = empty($user['email']) ? $dinguser['email'] : $user['email']; 
                        C::t('user') -> update($user['uid'], $setarr);
                        $ret += 1;
                    } 
                }
                else {//创建用户信息
                    $data = array(
                        "name" => $user['username'],
                        "department" => $ding_orgids,
                        //"position" => '',
                        "email" => $user['email'], 
                    );
                    if ($user['phone']) {
                        $data['mobile'] = $user['phone'];
                    }else{
                        if($back) {
                            exit(json_encode(array('msg' => 'continue', 'start' => $user['uid'], 'message' => $user['username'] . ' <span class="success">手机号为空同步失败</span>')));
                        }else{
                            continue;
                        }
                    }
                    if ($profile['telephone']) {
                        $data['tel'] = $profile['telephone'];
                    }
                     
                    //创建用户前查询企业微信端所有用户，判断是否微信账户重名 如email 或者 mobile相同视为同一用户　则更新信息
                    loadcache("ding_down_userlist",true);
                    $dinguserlist = getglobal('cache/ding_down_userlist');
                    if($i==0 || !$dinguserlist){
                        $userlist=array();
                        $userlist = $ding->getUserList(1);//初始化时获取1下面的用户
                        $dingdepart = $ding->listDept();
                        if( $dingdepart['department'] ){
                            $dingdepartids=array();
                            foreach ($dingdepart['department'] as $key=> $value) {
                                $zjuserlist = $ding->getUserList( $value["id"] );
                                if( $zjuserlist!==false){
                                    $userlist =array_merge($userlist, $zjuserlist);
                                }
                            }
                        }
                        $dinguserlist =$userlist;
                        savecache("ding_down_userlist",$dinguserlist);
                    } 
                     
                    $dinguser=array();
                    if( $dinguserlist ){
                        foreach($dinguserlist as $k=>$v ){
                            if(  $v["email"] && $user["email"]==$v["email"] ){
                                $dinguser=$v;
                                break;
                            }
                            if( $v["mobile"] && $user["phone"]==$v["mobile"] ){
                                $dinguser=$v;
                                break;
                            }
                        }
                    }
                     
                    $dinguserinfo=array();
                    if( $dinguser ){//判断是否已存在手机号或者邮箱，如果有则认定为是同一个账户，不需要重新创建
                        $data["userid"]=$dinguser["userid"]; 
                        $result = $ding->updateUser($data);
                        
                        if( $result ){
                            $dinguserinfo = $ding->getUser( $dinguser["userid"] );
                        } 
                    }else{//查询不到钉钉端企业用户，重新创建 重新创建时判断是否重名，如果重名重新命名，直到不重名 
                        $result = $ding->createUser($data); 
                        if( $result ){
                            $dinguserinfo = $ding->getUser( $result["userid"] );
                        } 
                    }
                     
                    if ( $result ) {
                        $dinguserinfo["uid"]=$user['uid'];
                        self::dingSynuser($dinguserinfo); 
                        $ret += 1; 
                    }  
                }
                $i++;
            }
        }
        return $ret;
    }
    
    //钉钉端向dzz同步部门
    public static function dingSyndepartment( $arr=array() ){
        global $_G;
        if( $arr && $_G['setting']['Ding_status'] && $_G['setting']['Ding_CorpID'] && $_G['setting']['Ding_CorpSecret']){
			$data=array(
				'orgid'=>$arr['orgid'],
				'ding_deptment_name'=>$arr["name"],
				'ding_deptment_id'=>$arr['id'],
				'ding_deptment_pid'=>$arr['parentid'], 
				'update_time'=>TIMESTAMP,
			);
            $ddeparmentinfo=C::tp_t("ding_organization")->where("ding_deptment_id=".$arr['id'])->find();
            if( $ddeparmentinfo){
                $dpid=$ddeparmentinfo["id"];
                $result=C::tp_t("ding_organization")->where("id=".$did)->save($data);
            }else{
                $data['create_time']=TIMESTAMP;
                $dpid=C::tp_t("ding_organization")->add( $data );
            } 
            return $dpid;
        }
    }
    
    //dzz向钉钉端同步部门 机构端新增或者修改部门时调用
    public static function syntolineDepartment( $arr=array() ,$action=null,$force=false){ 
        global $_G;
		if(!$force && !$_G['setting']['Ding_auto_syn_up']) return ;
        if( $action=="del"){ return self::syntolineDeldepartment($arr);  }//删除与新增修改用的是用一个钩子
        if( $arr && $_G['setting']['Ding_status'] && $_G['setting']['Ding_CorpID'] && $_G['setting']['Ding_CorpSecret']){
            $setting_ding = $_G['setting']; 
            $ding = self::dingClass();
            if( $ding===false ){ 
                return $ret;
            }
             
            //先获取钉钉端所有部门，方便后面排除已存在
            //loadcache("ding_down_departmentlist",true);
            $dingdepart = getglobal('cache/ding_down_departmentlist');
            $dingdepartlist = array();
            $i=0;
            $deparr = (array)$arr;
            foreach($deparr as $arrid){
                if($i==0 || !$dingdepart){
                    $dingdepart = $ding -> listDept(); 
                    if ($dingdepart ) {
                        savecache("ding_down_departmentlist",$dingdepart); 
                    } else {
                        runlog('dinglog', 'errCode:' . $ding-> errCode . '; errMsg:' . $ding-> errMsg);
                        return false;
                    }
                } 
                foreach ($dingdepart['department'] as $value) {
                    $dingdepartlist[$value['id']] = $value;
                }
                
                $arr=C::tp_t("organization")->where("orgid=".$arrid)->find();
                if(!$arr || $arr["type"]>0) continue;
                $ddeparmentinfo=C::tp_t("ding_organization")->where("orgid=".$arr['orgid'])->find();
               
                $add=false;
                $update=false;
                if( $arr["forgid"]==0 ){
                    $parentid=1;
                } else{
                    $parentdeparmentinfo=C::tp_t("ding_organization")->where("orgid=".$arr['forgid'])->find();
                    $parentid=$parentdeparmentinfo["ding_deptment_id"];
                }
                $data=array( "name"=>$arr["orgname"], "parentid"=>$parentid );
                $dingdepartinfo=array();
                if( $ddeparmentinfo ){//查询是否已经绑定钉钉部门 更新或新增
                    if( isset($dingdepartlist[$ddeparmentinfo["ding_deptment_id"]])){
                        $data["id"]=$ddeparmentinfo["ding_deptment_id"];
                        $update=true;//更新到钉钉
                    }else{
                        $add=true;//新增到钉钉
                    }
                }else{
                    $add=true;//新增到钉钉
                }
                 
                if($add){ 
                    //插入绑定数据到 ding_organization表
                    $adddata=array(
                        'orgid'=>$arr['orgid'],
                        'ding_deptment_name'=>$arr["orgname"],
                        'ding_deptment_id'=>0,
                        'ding_deptment_pid'=>$parentid, 
                        'update_time'=>TIMESTAMP,
                        'create_time'=>TIMESTAMP
                    );
                    $dpid=C::tp_t("ding_organization")->add( $adddata );
                    $return = $ding ->createDept($data);
                   
                    if( $return ){
                        $result=C::tp_t("ding_organization")->where("id=".$dpid)->save( array('ding_deptment_id'=>$return["id"] ) ); 
                        runlog('dinglog', $arr['orgname'] .lang('update_success') );
                        return $return["id"];
                    }else{
                        if ($ding->errCode == '60008') {//部门名称已存在 导致但本地未保存或保存的钉钉id错误导致的问题
                            foreach ($dingdepartlist as $value) {
                                if ($value['name'] == $data['name'] && $value['parentid'] = $data['parentid']) { 
                                    $result=C::tp_t("ding_organization")->where("id=".$dpid)->save( array('ding_deptment_id'=>$value["id"], 'ding_deptment_pid'=>$value["parentid"]) ); 
                                    runlog('dinglog', $arr['orgname'] .lang('update_success') );
                                    return $value["id"];
                                }
                            }
                        }else{
                            runlog('dinglog',$arr['orgname'].', errCode:' . $ding-> errCode . '; errMsg:' . $ding-> errMsg);
                            return false;
                        } 
                    } 
                }
                elseif($update){
                    $return = $ding ->updateDept($data);
                    //更新绑定数据到 ding_organization表
                    if( $return ){
                         $updata=array(
                            'orgid'=>$arr['orgid'],
                            'ding_deptment_name'=>$arr["orgname"],
                            'ding_deptment_id'=>$data["id"],
                            'ding_deptment_pid'=>$data["parentid"],
                            'update_time'=>TIMESTAMP 
                        );
                        $did= $data["id"]; 
                        $dpid=C::tp_t("ding_organization")->where("id=".$ddeparmentinfo["id"])->save($updata); 
                        runlog('dinglog', $arr['orgname'] .lang('update_success') );
                        return $did;
                    }else{ 
                        runlog('dinglog',$arr['orgname'].', errCode:' . $ding-> errCode . '; errMsg:' . $ding-> errMsg);
                        return false;
                    }
                }
                $i++;
            }
        } 
    }
    
    //dzz端删除部门时候同步删除钉钉端部门 (如果部门下面有用户，则移到根部门下)
    public static function syntolineDeldepartment( $arr=array() ){ 
        global $_G;
        $ret = 0;
        if( $arr && $_G['setting']['Ding_status'] && $_G['setting']['Ding_CorpID'] && $_G['setting']['Ding_CorpSecret']){
            $setting_ding = $_G['setting'];  
            $arr = (array)$arr;
            
            $ding = self::dingClass();
            if( $ding===false ){ 
                return $ret;
            }
             
            foreach($arr as $key=>$orgid){
                $ding_depart=C::tp_t("ding_organization")->where("orgid=".$orgid)->find();
                if(!$ding_depart) continue;//判断本地是否绑定了钉钉
                $result_ding = $ding->getDept( $ding_depart["ding_deptment_id"] );
                if(!$result_ding) continue;//判断钉钉端是否有该部门

                $result_sub = $ding->listDeptid( $ding_depart["ding_deptment_id"] ); 
                if( $result_sub && !$result_sub["sub_dept_id_list"]){//判断没有子部门的时候才执行后续操作
                    //获取该部门下面的用户id
                    $userlist = $ding->getUserList( $ding_depart["ding_deptment_id"]  );//获取该下面的用户
                    if( $userlist!==false ){//正确返回
                        if( $userlist ){//返回中是有用户 则先移除用户  不用判断是否有子部门
                            //移动到跟目录下
                            foreach($userlist as $ddkey=>$dduser){
                                $nowarr = array(1,$orgid);
                                $diff_depart=array_diff($dduser["department"],$nowarr);
                                if( !$diff_depart ){
                                    $diff_depart=array(1); 
                                }
                                $data = array( "userid" => $dduser["userid"],"name"=>$dduser["name"], "department"=>$diff_depart  );
                                if ($ding -> updateUser($data)) {
                                    runlog('dinglog', $dduser['name'] .lang('delete_success') );
                                }else{
                                    runlog('dinglog',$dduser['name'].', errCode:' . $ding-> errCode . '; errMsg:' . $ding-> errMsg); 
                                }
                            }
                        }
                        //删除部门
                        $result = $ding->deleteDept( $ding_depart["ding_deptment_id"] );
                        if ( $result ) { 
                            $result2=C::tp_t("ding_organization")->where("id=".$ding_depart["id"])->delete();
                            runlog('dinglog', $ding_depart['ding_deptment_name'] .lang('delete_success') );
                            $ret++;
                        }else{
                            runlog('dinglog',$ding_depart['ding_deptment_name'].', errCode:' . $ding-> errCode . '; errMsg:' . $ding-> errMsg); 
                        }
                    } 
                }
            }
        }
        return $ret;
    }
    
    //dzz端删除用户时候同步删除钉钉端用户
    public static function syntolineDeluser ( $arr=array() ){
        global $_G;
        $ret = 0;
        if( $arr && $_G['setting']['Ding_status'] && $_G['setting']['Ding_CorpID'] && $_G['setting']['Ding_CorpSecret']){
            $setting_ding = $_G['setting'];  
            $uids = (array)$arr;
            $ding = self::dingClass();
            if( $ding===false ){ 
                return $ret;
            }
            foreach($uids as $key=>$uid){
                $ding_user=C::tp_t("ding_user")->where("uid=".$uid)->find();
                if(!$ding_user) continue;//判断本地是否绑定了钉钉
                $result_user = $ding->getUser( $ding_user["ding_userid"] );
                if(!$result_user) continue;//判断钉钉端是否有该用户
                //以下待确定dzz端删除逻辑
               
                $result = array_diff($result_user["department"],array(1)); 
                if( !$result ){//仅仅只在根部门才删除 因为dzz只在无机构时才真删用户
                    $resultdel = $ding->deleteUser( $ding_user["ding_userid"] );
                    if( $resultdel ){
                        //删除本地绑定关系
                        $result2=C::tp_t("ding_user")->where("id=".$ding_user["id"])->delete();
                        runlog('dinglog', $ding_user['ding_name'] .lang('delete_success') );
                        $ret++;
                    }
                    else{ 
                        runlog('dinglog',$ding_user['ding_name'].', errCode:' . $ding-> errCode . '; errMsg:' . $ding-> errMsg);
                    }
                }
            }
        }
        return $ret;
    }
    
    //登录钩子
    public static function thirdLogin( $arr=array() ){
        global $_G;
        if( $_G['setting']['Ding_Appid'] && $_G['setting']['Ding_status'] && $_G['setting']['Ding_AppSecret'] && $_G['setting']['Ding_loginstatus']){
           $ding = self::dingClass();
            if( $ding===false ){ 
                return '';
            }
            
            $url = getglobal('siteurl') ; 
            $callbackurl= getglobal('siteurl').'dingredirect/' . dzzencode($url);//需要钉钉回调伪静态保持一致 
            //$callbackurl= 'http://test.dzzoffice.com/index.php?mod=dingtalk&op=dingredirect&url=' . dzzencode($url);
          
            $loginmode = $_G['setting']['Ding_loginmode']; 
            if( $loginmode==2 ){
                $url= getglobal('siteurl') . 'index.php?mod=dingtalk&op=loginmode';
                  echo '<a class="ding-login" href="'.$url.'" title="'.lang('dingdingloging',array(),null,'dzz/dingtalk').'"><img src="dzz/dingtalk/images/ding_logo.png" /></a>'; 
            }
            elseif( $loginmode==1 ){//二维码方式
                $qrcodeurl = $ding->getQrcodeRedirect( $callbackurl ,'STATE','snsapi_login');
                echo '<a class="ding-login" href="'.$qrcodeurl.'" title="'.lang('dingdingloging',array(),null,'dzz/dingtalk').'"><img src="dzz/dingtalk/images/ding_logo.png" /></a>'; 
            }else{//账号密码方式
                $url = $ding->getOauthRedirect( $callbackurl ,'STATE','snsapi_login');
                echo '<a class="ding-login" href="'.$url.'" title="'.lang('dingdingloging',array(),null,'dzz/dingtalk').'"><img src="dzz/dingtalk/images/ding_logo.png" /></a>';
            } 
        }
    }
    
    //钉钉发送通知消息
    public static function onlineNotification( $notification_id=0 ){
        global $_G;
        if( $_G['setting']['Ding_Appid'] && $_G['setting']['Ding_CorpSecret'] && $_G['setting']['Ding_status'] ){ 
            if($notification_id>0){//新增 
                $data=C::t('notification')->fetch($notification_id);
                //判断当前消息用户是否已经绑定钉钉 及应用是否本地有绑定
                $user=C::tp_t('ding_user')->where("uid=".$data["uid"])->find();
                $bindappinfo=C::tp_t('ding_app')->where("appid=".$data["from_id"])->find();
                if($user &&  $bindappinfo){
                    //关联消息
                    $adddata = array(
                        'nid' => $data['id'],
                        'update_time'=>TIMESTAMP,
                        'is_send'=>0, 
                    ); 
                    $dmsg=C::tp_t("ding_msg")->where("nid='".$data['id']."'")->find();
                    if( $dmsg ){
                        $msgid=$dmsg["id"];
                        $adddata['update_time']=TIMESTAMP;
                        $res=C::tp_t("ding_msg")->where("id=".$msgid)->save($adddata);
                    }else{
                        $adddata['create_time']=TIMESTAMP;
                        $msgid=C::tp_t("ding_msg")->add( $adddata ); 
                    } 
                } 
            }
            else{//发送 
                $msglist =C::tp_t("ding_msg")->where( array("is_send"=>0) )->limit(10)->select();
                
                if( $msglist ){ 
                    $ding = self::dingClass();
                    foreach($msglist as $k=>$v){
                        $data=C::t('notification')->fetch($v["nid"]);
                        
                        $user=C::tp_t('ding_user')->where("uid=".$data["uid"])->find();
                        $bindappinfo=C::tp_t('ding_app')->where("appid=".$data["from_id"])->find();
                        if($user && $bindappinfo){ 
                            $msg=array(
                                "touser"=>$user["ding_userid"],
                                "agentid"=>$bindappinfo["agentid"],
                                "msgtype"=>"action_card",//ActionCard消息
                                "action_card"=>array(
                                    "title"=> $data['title'],
                                    "markdown"=> "**".$data['title']."**\n\r".getstr($data['wx_note'],0,0,0,0,-1)."......",
                                    "single_title"=> "查看详情", 
                                    "single_url"=> getglobal('siteurl').'dingredirect/'.dzzencode($data['redirecturl'])."?fromapp=1"
                                    //$ding->getOauthRedirect( getglobal('siteurl').'dingredirect/'.dzzencode($data['redirecturl']),"STATE","snsapi_auth",$bindappinfo["agentid"])//这里的链接请注意是否配置了伪静态
                                )
                            ); 
                            $ret = $ding->sendMessage( $msg );
                            if( $ret ){
                                $res=C::tp_t("ding_msg")->where("id=".$v["id"])->save(array("is_send"=>1,"send_num"=> ($v["send_num"]+1),"update_time"=>TIMESTAMP ));
                                return true;
                            }else{
                                $res=C::tp_t("ding_msg")->where("id=".$v["id"])->save(array("send_num"=> ($v["send_num"]+1),"update_time"=>TIMESTAMP));
                                runlog('dinglog','send msg(id:'.$v["id"].') error, errCode:' . $ding-> errCode . '; errMsg:' . $ding-> errMsg);
                                return false;
                            } 
                        }  
                    } 
                }
            }
        }
        return false;
    }
    
    //模板头部包含脚本
    public function headerTpl(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //钉钉pc客户端打开时需要载入以下js用于判断登录
        if (preg_match("/dingtalk-win/i", $agent)) {
            echo '<script type="text/javascript" src="https://g.alicdn.com/dingding/dingtalk-pc-api/2.7.0/index.js"></script>';
        }
    }
   
    public static function dingClass($arr=array()){
        global $_G;
        $obj = false;
        
         $objdata = array(
            'corpid' =>  $_G['setting']['Ding_CorpID'],
            'corpsecret' =>  $_G['setting']['Ding_CorpSecret'],
            'appid'=>$_G['setting']['Ding_Appid'],
            'appsecret'=>$_G['setting']['Ding_AppSecret']
        );
        if($arr){
            $objdata=array_merge($objdata,$arr);
        }
        
        $curent_file = dirname( __FILE__);
        $pathinfo = explode(DZZ_ROOT,$curent_file); 
        if( $pathinfo[1]){
            $pathinfo2 = explode("classes",$pathinfo[1]);
            if($pathinfo2[0]){
                $dingclassfile = DZZ_ROOT.$pathinfo2[0]."class/class_Ding.php";
                if( file_exists($dingclassfile)){
                    include_once($dingclassfile);
                    $obj = new \Ding($objdata );
                }
                else{
                    runlog('dinglog', 'file: '.$dingclassfile.' is none');
                }
            }
        } 
        return $obj;
    }
}