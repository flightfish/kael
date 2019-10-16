<?php

namespace usercenter\modules\admin\models;

use common\libs\UserToken;
use common\models\LogPlatform;
use common\models\Platform;
use common\models\Role;
use common\models\UserCenter;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;

class KaelPlatform extends RequestBaseModel
{

    const SCENARIO_LIST = "SCENARIO_LIST";
    const SCENARIO_ADD = "SCENARIO_ADD";
    const SCENARIO_EDIT = "SCENARIO_EDIT";
    const SCENARIO_DEL = "SCENARIO_DEL";


    public $page = 1;
    public $pagesize = 20;


    public $platform_id;
    public $platform_url;
    public $platform_name;
    public $env_type;
    public $platform_icon;
    public $is_show;
    public $admin_user;
    public $ip_limit;


    public function rules()
    {
        return array_merge([
            [['page','pagesize','env_type','is_show','admin_user','ip_limit','platform_id'], 'integer'],
            [['platform_name','platform_url','platform_icon'], 'string'],
            [['page','pagesize'], 'required', 'on' => self::SCENARIO_LIST],
            [['platform_id','env_type','is_show','admin_user','ip_limit','platform_name','platform_url','platform_icon'], 'required', 'on' => self::SCENARIO_EDIT],
            [['platform_id'], 'required', 'on' => self::SCENARIO_DEL],
            [['env_type','is_show','admin_user','ip_limit','platform_name','platform_url','platform_icon'], 'required', 'on' => self::SCENARIO_ADD],
        ], parent::rules());
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LIST] = ['page','pagesize','platform_name','env_type'];
        $scenarios[self::SCENARIO_DEL] = ['platform_id'];
        $scenarios[self::SCENARIO_EDIT] = ['platform_id','env_type','is_show','admin_user','ip_limit','platform_name','platform_url','platform_icon'];
        $scenarios[self::SCENARIO_ADD] = ['env_type','is_show','admin_user','ip_limit','platform_name','platform_url','platform_icon'];
        return $scenarios;
    }


    public function checkUserAuth()
    {
        $platformInfo = Platform::findOneById(1);
        !empty($platformInfo['allow_ips']) && $platformInfo['allow_ips'] = $platformInfo['allow_ips'].','.(Yii::$app->params['allow_ips']);
        $clientIPAllow = explode(',',$platformInfo['allow_ips']);
        $clientIP = UserToken::getRealIP(false);
        if(!empty($platformInfo['allow_ips']) && !in_array($clientIP,$clientIPAllow)){
            throw new Exception('该平台仅限公司内网访问',Exception::ERROR_COMMON);
        }
        if(!in_array($this->user['admin'],[Role::ROLE_ADMIN])){
            throw new Exception('权限不足',Exception::ERROR_COMMON);
        }
        return true;
    }

    /**
     * @return array
     * @throws Exception
     * 分页列表
     */
    public function pagelist()
    {

        $this->checkUserAuth();

        $page = max(1,$this->page);
        $pagesize = max(1,$this->pagesize);
        $where = [];
        $whereArr = [];
        !empty($this->platform_name) && $whereArr[] = ['like','platform_name',$this->platform_name];
        !empty($this->env_type) && $this->env_type > 0 && $where['env_type']=$this->env_type;
        $list = Platform::findPageList($page,$pagesize,$where,'platform_id desc','*',$whereArr);
        $count = Platform::findCount($where,$whereArr);
        $adminUserIds = array_unique(array_column($list,'admin_user'));
        $userInfoList = UserCenter::findListById($adminUserIds);
        $userIdToName = array_column($userInfoList,'username','id');

        foreach($list as $k=>$v){
            $v['admin_user_name'] = $userIdToName[$v['admin_user']] ?? '未分配';
            $v['ip_limit'] = empty($v['allow_ips']) ? 0 : 1;
            $list[$k] = $v;
        }

        $retData = [
            'page' => $page,
            'total' => $count,
            'list' => array_values($list),
        ];
        return $retData;
    }


    public function del()
    {
        $this->checkUserAuth();
        $platformInfo = Platform::findOneById($this->platform_id);
        if(empty($platformInfo)){
            throw new Exception("平台不存在",Exception::ERROR_COMMON);
        }
        $trans = Platform::getDb()->beginTransaction();
        try{
            Platform::updateAll(['status'=>Platform::STATUS_INVALID],['platform_id' => $this->platform_id]);
            if(empty($platfromId)){
                throw new Exception("删除失败");
            }
            LogPlatform::log(LogPlatform::DEL,$platfromId,$this->user['id'],'');
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            throw $e;
        }
        return [];
    }

    public function add(){
        $this->checkUserAuth();
        $param = [
            'platform_name'=>$this->platform_name,
            'platform_url'=>$this->platform_url,
            'platform_icon'=>$this->platform_icon,
            'allow_ips'=>$this->ip_limit && $this->ip_limit > 0 ? '127.0.0.1' : '',
            'is_show'=>$this->is_show,
            'admin_user'=>$this->admin_user,
            'env_type'=>$this->env_type
        ];
        $trans = Platform::getDb()->beginTransaction();
        try{
            $platfromId = Platform::add($param);
            if(empty($platfromId)){
                throw new Exception("保存失败");
            }
            LogPlatform::log(LogPlatform::ADD,$platfromId,$this->user['id'],$param);
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            throw $e;
        }
    }

    public function edit(){
        $this->checkUserAuth();
        $platformInfo = Platform::findOneById($this->platform_id);
        if(empty($platformInfo)){
            throw new Exception("平台不存在",Exception::ERROR_COMMON);
        }
        $param = [
            'platform_name'=>$this->platform_name,
            'platform_url'=>$this->platform_url,
            'platform_icon'=>$this->platform_icon,
            'allow_ips'=>$this->ip_limit && $this->ip_limit > 0 ? '127.0.0.1' : '',
            'is_show'=>$this->is_show,
            'admin_user'=>$this->admin_user,
            'env_type'=>$this->env_type
        ];
        $trans = Platform::getDb()->beginTransaction();
        try{
            $platfromId = Platform::updateAll($param,['platform_id'=>$this->platform_id]);
            if(empty($platfromId)){
                throw new Exception("保存失败");
            }
            LogPlatform::log(LogPlatform::EDIT,$platfromId,$this->user['id'],$param);
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            throw $e;
        }
    }


}
