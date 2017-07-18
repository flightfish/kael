<?php

namespace usercenter\modules\admin\models;

use common\libs\Constant;
use common\models\CommonModulesUser;
use common\models\Department;
use common\models\Platform;
use common\models\RelateAdminDepartment;
use common\models\RelateDepartmentPlatform;
use common\models\RelateUserPlatform;
use common\models\Role;
use common\models\UserCenter;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;

class Departments extends RequestBaseModel
{

    const SCENARIO_LIST = "SCENARIO_LIST";
    const SCENARIO_EDIT = "SCENARIO_EDIT";
    const SCENARIO_DEL = "SCENARIO_DEL";
    const SCENARIO_IMPORT = "SCENARIO_IMPORT";
    const SCENARIO_CHECKMOBILE = "SCENARIO_CHECKMOBILE";
    const SCENARIO_USER_UPLOAD = "SCENARIO_USER_UPLOAD";
    const SCENARIO_USER_DOWNLOAD = "SCENARIO_USER_DOWNLOAD";
    const SCENARIO_PLAT_BY_DEPARTMENT = "SCENARIO_PLAT_BY_DEPARTMENT";
    public $page = 1;
    public $pagesize = 20;
    public $data = [];
    public $id;
    public $type;
    public $mobile;
    public $filter = [];
    public $user_source = "admin";
    public $user_type = "1";
    public $is_admin = "0";
    public $department_id;
    public $user_id;
    public $platform_list;


    public function rules()
    {
        return array_merge([
            [['page', 'pagesize', 'id','department_id','user_id'], 'integer'],
            [['type', 'mobile'], 'string'],
            [[ 'user_id','department_id'], 'required', 'on' => self::SCENARIO_EDIT],
            [['id'], 'required', 'on' => self::SCENARIO_DEL],
            [['page', 'pagesize'], 'required', 'on' => self::SCENARIO_LIST],
            [['data', 'filter','platform_list'], 'safe']
        ], parent::rules());
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return $scenarios;
    }


    public function checkUserMobile()
    {
        $userInfo = UserCenter::find()->where(['mobile' => $this->mobile, 'status' => UserCenter::STATUS_VALID])->one();
        if(!empty($userInfo)){
            //已存在
            throw new Exception('用户已存在，无法新增',Exception::ERROR_COMMON);
        }
        if (empty($userInfo)) {
            return false;
        }
        return $userInfo;
    }


    public function checkUserAuth()
    {
        if(!in_array($this->user['admin'],[Role::ROLE_ADMIN])){
            throw new Exception('权限不足',Exception::ERROR_COMMON);
        }
        return true;
    }

    public function roleList()
    {

        $roleList = Role::findAllList();
        return $roleList;
    }

    public function platformList()
    {

        $platList = Platform::findAllList();
        return $platList;
    }

    public function platformListByAdminDepartment()
    {
        if($this->user['admin'] == Role::ROLE_ADMIN){
            if($this->department_id == -1){
                $platList = [];
//                $platList = Platform::findAllList();
            }else{
                $departmentAllowPlat = RelateDepartmentPlatform::findListByDepartment($this->department_id);
                $platId = array_column($departmentAllowPlat,'platform_id');
                $platList = Platform::findListById($platId);
            }
        }elseif($this->user['admin'] == Role::ROLE_DEPARTMENT_ADMIN){
            if($this->department_id == -1){
                $platList = [];
            }else{
                $departmentAllowPlat = RelateDepartmentPlatform::findListByDepartment($this->department_id);
                $departmentAllowPlatSelf = RelateAdminDepartment::findListByAdminDepartment($this->user['id'],$this->department_id);
                $platIdAllow = array_column($departmentAllowPlat,'platform_id');
                $platIdAllowSelf = array_column($departmentAllowPlatSelf,'platform_id');
                $platId = array_intersect($platIdAllow,$platIdAllowSelf);
                $platList = Platform::findListById($platId);
            }

        }else{
            $platList = [];
        }
        return $platList;
    }

    public function departmentList()
    {

        $departmentList = Department::findAllList();
        return $departmentList;
    }

    public function departmentListByAdmin()
    {
        if($this->user['admin'] == Role::ROLE_DEPARTMENT_ADMIN){
            $departmentList = RelateAdminDepartment::findListByAdmin($this->user['id']);
            $departmentIds = array_column($departmentList,'department_id');
            $departmentList = Department::findListById($departmentIds);
            return $departmentList;
        }elseif($this->user['admin'] == Role::ROLE_ADMIN){
            $departmentList = Department::findAllList();
            return $departmentList;
        }else{
            return [];
        }
    }

    /**
     * @return array
     * @throws Exception
     * 分页列表
     */
    public function pagelist()
    {

        $this->checkUserAuth();

        $departmentList = Department::find()->where(['status'=>Department::STATUS_VALID]);
        $search = !empty($this->filter['search']) ? trim($this->filter['search']) : "";
        if(!empty($search)){
            $departmentList = $departmentList->andWhere(['like','department_name',$search]);
        }
        $departmentList = $departmentList->indexBy('department_id')->asArray(true)->all();
        $total = count($departmentList);
        $departmentAdminList = RelateAdminDepartment::find()->where(['status'=>Department::STATUS_VALID])->asArray(true)->all();
        $platformList  = Platform::findAllList();
        $departmentPlatformList = RelateDepartmentPlatform::find()->where(['status'=>RelateDepartmentPlatform::STATUS_VALID])->asArray(true)->all();
        $userIds = array_column($departmentAdminList,'user_id');
        $userList = UserCenter::findListById($userIds);
        $departmentExtAdmin = [];
        $departmentExtPlatform = [];
        foreach($departmentAdminList as $k=>$v){
            !isset($departmentExtAdmin[$v['department_id']]) && $departmentExtAdmin[$v['department_id']] = [];
            !isset($departmentExtAdmin[$v['department_id']][$v['user_id']]) && $departmentExtAdmin[$v['department_id']][$v['user_id']] = [
                'id'=>$v['user_id'],
                'username'=>$userList[$v['user_id']]['username']
            ];
            $departmentExtAdmin[$v['department_id']][$v['user_id']]['platform_list'][] = [
                'platform_id'=>$platformList[$v['platform_id']]['platform_id'],
                'platform_name'=>$platformList[$v['platform_id']]['platform_name'],
            ];
        }
        foreach($departmentPlatformList as $k=>$v){
            $departmentExtPlatform[$v['department_id']][] = $platformList[$v['platform_id']];
        }

        foreach($departmentList as $k=>$v){
            $v['platform_list'] = isset($departmentExtPlatform[$v['department_id']]) ? $departmentExtPlatform[$v['department_id']] : [];
            $v['admin_list'] = isset($departmentExtAdmin[$v['department_id']]) ? array_values($departmentExtAdmin[$v['department_id']]) : [];
            $departmentList[$k] = $v;
        }

        $retData = [
            'page' => $this->page,
            'total' => $total,
            'list' => array_values($departmentList),
        ];
        return $retData;
    }


    public function del()
    {
        $this->checkUserAuth();
        Department::updateAll(['status'=>Department::STATUS_INVALID],['department_id' => $this->id]);
        return [];
    }


    /**
     * @return bool|int
     * @throws Exception
     * @throws \Exception
     * 仅创建者可以修改
     */
    public function edit()
    {
        $this->checkUserAuth();

        if (empty($this->user_id) || $this->user_id == -1) {
            throw new Exception('请选择管理员', Exception::ERROR_COMMON);
        }
        if (empty($this->department_id) || $this->department_id == -1) {
            throw new Exception('部门不存在', Exception::ERROR_COMMON);
        }

        if(empty($this->platform_list)){
            //不存在
            RelateAdminDepartment::updateAll(['status'=>RelateAdminDepartment::STATUS_INVALID],['user_id'=>$this->user_id,'department_id'=>$this->department_id]);
        }else{
            //存在
            $column = ['user_id','department_id','platform_id'];
            $rows = [];
            foreach($this->platform_list as $platformId){
                $rows[] = [$this->user_id,$this->department_id,$platformId];
            }
            RelateAdminDepartment::batchInsertAll(RelateAdminDepartment::tableName(),$column,$rows,RelateAdminDepartment::getDb(),'REPLACE');
        }

    }


}
