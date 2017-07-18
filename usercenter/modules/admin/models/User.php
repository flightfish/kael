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

class User extends RequestBaseModel
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


    public function rules()
    {
        return array_merge([
            [['page', 'pagesize', 'id','department_id'], 'integer'],
            [['type', 'mobile'], 'string'],
            [['data', 'id'], 'required', 'on' => self::SCENARIO_EDIT],
            [['mobile'], 'required', 'on' => self::SCENARIO_CHECKMOBILE],
            [['id'], 'required', 'on' => self::SCENARIO_DEL],
            [['page', 'pagesize'], 'required', 'on' => self::SCENARIO_LIST],
            [['department_id'],'required','on'=>self::SCENARIO_PLAT_BY_DEPARTMENT],
            [['data', 'filter'], 'safe']
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

    public function checkSuperAuth(){
        if(!in_array($this->user['admin'],[Role::ROLE_ADMIN])){
            throw new Exception('权限不足',Exception::ERROR_COMMON);
        }
        return true;
    }


    public function checkUserAuth($userId=-1)
    {
        if(!in_array($this->user['admin'],[Role::ROLE_ADMIN,Role::ROLE_DEPARTMENT_ADMIN])){
            throw new Exception('权限不足',Exception::ERROR_COMMON);
        }
        if($userId != -1){
            if($this->user['admin'] == Role::ROLE_DEPARTMENT_ADMIN){
                $toUserInfo = UserCenter::findOneById($userId);
                $deparmentId = $toUserInfo['department_id'];
                $isAuth = RelateAdminDepartment::findListByAdminDepartment($userId,$deparmentId);
                if(empty($isAuth)){
                    throw new Exception('权限不足',Exception::ERROR_COMMON);
                }
            }
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
        $search = !empty($this->filter['search']) ? trim($this->filter['search']) : "";
        $where = [];
        isset($this->filter['role']) && $this->filter['role'] != -1 && $where['admin'] = $this->filter['role'];
        isset($this->filter['department']) && $this->filter['department'] != -1 && $where['department_id'] = $this->filter['department'];
        if(isset($this->filter['department']) && $this->filter['department'] == -1){
            if($this->user['admin'] == Role::ROLE_DEPARTMENT_ADMIN){
                $departmentList = RelateAdminDepartment::findListByAdmin($this->user['id']);
                $where['department_id'] = array_column($departmentList,'department_id');
            }
        }
        isset($this->filter['subject']) && is_numeric($this->filter['subject']) && $where['subject'] = $this->filter['subject'];
        isset($this->filter['grade_part']) && is_numeric($this->filter['grade_part']) && $where['grade_part'] = $this->filter['grade_part'];

        $leftjoin = [];
        if(isset($this->filter['platform']) && $this->filter['platform'] != -1){
            $leftjoin[] = [RelateUserPlatform::tableName().' b','a.id = b.user_id'];
            $where['b.platform_id'] = $this->filter['platform'];
        }
        $userList = UserCenter::findUserSearch($this->page,$this->pagesize,$search,$where,$leftjoin);
        $total  = UserCenter::findUserSearchCount($search);
        //部门 权限
        $roleEntity = Role::findAllList();
        $userIds = array_column($userList,'id');
        $platformList = RelateUserPlatform::findListByUserPlatform($userIds);
        $relateAdminDepart = RelateAdminDepartment::findListByAdmin($userIds);
        //实体
        $departmentEntity  = Department::findAllList();
        $platformEntity = Platform::findAllList();
        //拼装
        foreach($userList as $k=>$v){
            $v['subject_name'] = empty(Constant::ENUM_SUBJECT[$v['subject']]) ? "未知" : Constant::ENUM_SUBJECT[$v['subject']];
            $v['grade_part_name'] = empty(Constant::ENUM_GRADE_ALL[$v['grade_part']]) ? "未知" : Constant::ENUM_GRADE_ALL[$v['grade_part']];
            $v['admin_department_list'] = [];
            $v['platform_list'] = [];
            $v['role_id'] = $v['admin'];
            $v['role_name'] = isset($roleEntity[$v['admin']]) ?$roleEntity[$v['admin']]['role_name'] : "未知";
            $v['department_name'] = isset($departmentEntity[$v['department_id']]) ? $departmentEntity[$v['department_id']]['department_name'] : "未知";
            $userList[$k] = $v;
        }
        foreach($platformList as $v){
            if(empty($platformEntity[$v['platform_id']])){
                continue;
            }
            $platformInfo = $platformEntity[$v['platform_id']];
            $userList[$v['user_id']]['platform_list'][] = [
                'platform_id'=>$v['platform_id'],
                'platform_name'=>$platformInfo['platform_name'],
            ];
        }
        foreach($relateAdminDepart as $v){
            if(empty($userList[$v['user_id']]['admin_department_list'][$v['department_id']])){
                $userList[$v['user_id']]['admin_department_list'][$v['department_id']] = [
                    'department_id'=>$v['department_id'],
                    'department_name'=>$departmentEntity[$v['department_id']]['department_name'],
                    'platform_id'=>[]
                ];
            }
            array_push($userList[$v['user_id']]['admin_department_list'][$v['department_id']]['platform_id'],$v['platform_id']);
        }

        foreach($userList as $k=>$v){
            $v['admin_department_list'] = array_values($v['admin_department_list']);
            $userList[$k] = $v;
        }


        $retData = [
            'page' => $this->page,
            'total' => $total,
            'list' => array_values($userList),
        ];
        return $retData;
    }


    public function del()
    {
        $this->checkUserAuth();
        UserCenter::updateAll(['status'=>UserCenter::STATUS_INVALID],['id' => $this->id]);
        return [];
    }


    /**
     * @return bool|int
     * @throws Exception
     * @throws \Exception
     * 仅创建者可以修改
     */
    public function mixAddEdit()
    {
        $this->checkUserAuth();

        if (empty($this->data['center']) || empty($this->data['center']['mobile'])) {
            throw new Exception('手机号不能为空', Exception::ERROR_COMMON);
        }
        if (empty($this->data['center']['username'])) {
            throw new Exception('用户名不能为空', Exception::ERROR_COMMON);
        }
        if(!isset($this->data['center']['admin']) || $this->data['center']['admin'] == -1){
            throw new Exception('请选择权限', Exception::ERROR_COMMON);
        }
        if(!isset($this->data['center']['sex']) || $this->data['center']['sex'] == -1){
            throw new Exception('请选择性别', Exception::ERROR_COMMON);
        }
        if(!isset($this->data['center']['user_type']) || $this->data['center']['user_type'] == -1){
            throw new Exception('请选择用户类型', Exception::ERROR_COMMON);
        }
        if(!isset($this->data['center']['department_id']) || $this->data['center']['department_id'] == -1){
            throw new Exception('请选择部门', Exception::ERROR_COMMON);
        }
        $this->data['center']['user_source'] = $this->user_source;
        $this->data['center']['password'] = !empty($this->data['center']['password']) ? md5($this->data['center']['password']) : "";
        if (empty($this->data['center']['password']))
            unset($this->data['center']['password']);

        //固定权限
        $platformListAllow = $this->platformListByAdminDepartment();
        $platformListAllow = array_column($platformListAllow,'platform_id');
        if(empty($this->data['platform_list'])){
            $this->data['platform_list'] = [];
        }else{
            $this->data['platform_list'] = array_intersect($this->data['platform_list'],$platformListAllow);
        }

        if (0 == $this->id) {
            //唯一性
            $old = UserCenter::find()->where(['mobile'=>$this->data['center']['mobile'],'status'=>UserCenter::STATUS_VALID])->one();
            if(!empty($old)){
                throw new Exception('手机号已存在', Exception::ERROR_COMMON);
            }
            if(!empty($this->data['center']['email'])){
                $old = UserCenter::find()->where(['email'=>$this->data['center']['email'],'status'=>UserCenter::STATUS_VALID])->one();
                if(!empty($old)){
                    throw new Exception('邮箱已存在', Exception::ERROR_COMMON);
                }
            }
            //新增
            $this->mobile = $this->data['center']['mobile'];
            $userInfo = $this->checkUserMobile();
            if(!empty($userInfo)){
                throw new Exception('用户已存在',Exception::ERROR_COMMON);
            }
            if (empty($this->data['center']['password'])) {
                $this->data['center']['password'] = md5("123456");
            }
            //新增
            $model = new UserCenter();
            foreach ($this->data['center'] as $k => $v) {
                $model->$k = $v;
            }
            $ret = $model->insert();
            $userId = $model->id;
            //权限
            RelateUserPlatform::batchAdd($userId,$this->data['platform_list']);
        } else {
            //编辑
            $oldOne = UserCenter::findOneById($this->id);
            if (empty($oldOne)) {
                throw new Exception("用户不存在", Exception::ERROR_COMMON);
            }
            //唯一性
            if($oldOne['mobile'] != $this->data['center']['mobile']){
                $old = UserCenter::find()->where(['mobile'=>$this->data['center']['mobile'],'status'=>UserCenter::STATUS_VALID])->one();
                if(!empty($old)){
                    throw new Exception('手机号已存在', Exception::ERROR_COMMON);
                }
            }
            if($oldOne['email'] != $this->data['center']['email']){
                if(!empty($this->data['center']['email'])){
                    $old = UserCenter::find()->where(['email'=>$this->data['center']['email'],'status'=>UserCenter::STATUS_VALID])->one();
                    if(!empty($old)){
                        throw new Exception('邮箱已存在', Exception::ERROR_COMMON);
                    }
                }
            }
            $ret = UserCenter::updateAll($this->data['center'], ['id' => $this->id]);
            RelateUserPlatform::updateAll(['status'=>RelateUserPlatform::STATUS_INVALID],['user_id' => $this->id,'platform_id'=>$platformListAllow]);
            RelateUserPlatform::batchAdd($this->id,$this->data['platform_list']);
        }
        return $ret;
    }

    /**
     *  批量导入用户
     */
    public function actionImportUser()
    {
        $this->checkUserAuth();
        $filePath = $_FILES['file']['tmp_name'][0];
        $PHPReader = new \PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件
        if (!$PHPReader->canRead($filePath)) { // 这里是用Reader尝试去读文件，07不行用05，05不行就报错。注意，这里的return是Yii框架的方式。
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                $errorMessage = "Can not read file.";
                array_push($error, $errorMessage);
                return $error;
            }
        }
        $objPHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例
        $data = $objPHPExcel->getSheet(0)->toArray();
        $error = array();
        $paramsUcenter = [];

        $allDepartment = Department::findAllList();

        foreach ($data as $k => $v) {
            if ($k == 0) {
                continue;//标题行
            }
            $v = array_map('strval',$v);
            $v = array_map('trim',$v);

            if (empty($v[0])) {
                array_push($error, '第' . ($k + 1) . '行，姓名不存在');
                continue;
            }
            if (empty($v[1])) {
                array_push($error, '第' . ($k + 1) . '行，手机号不存在');
                continue;
            }
            if (empty($v[4]) || !is_numeric($v[4])) {
                array_push($error, '第' . ($k + 1) . '行，性别不存在');
                continue;
            }
            if (empty($v[4]) || !isset($allDepartment[intval($v[4])])) {
                array_push($error, '第' . ($k + 1) . '行，部门不存在');
                continue;
            }
            if (!isset($v[10]) || !is_numeric($v[10])) {
                array_push($error, '第' . ($k + 1) . '行，请填写学科');
                continue;
            }
            if (!isset($v[11]) || !is_numeric($v[11])) {
                array_push($error, '第' . ($k + 1) . '行，请填写学段');
                continue;
            }

            $MobileOnly = UserCenter::findByMobile($v[1]);
            if (!empty($MobileOnly)) {
                array_push($error, '第' . ($k + 1) . '行，电话号码已存在不能重复添加');
                continue;
            }
            if(!empty($v[2])){
                $emailOnly = UserCenter::find()->where(['status'=>0,'email'=>$v[2]])->asArray(true)->one();
                if (!empty($emailOnly)) {
                    array_push($error, '第' . ($k + 1) . '行，邮箱已存在不能重复添加');
                    continue;
                }
            }

            $this->user_source = "admin";

            /*
             * $title = [
            '姓名',0
            '手机号',1
            '邮箱',2
            '性别(1:男；2:女)',3
            '所属部门('.$deparmentStr.')',4
            "身份证号",5
            '银行名称',6
            '银行区域',7
            '银行卡类型'8,
            '银行卡号',9
            '学科(0:数学；1:语文；2:英语；3:物理；4:化学；5:生物；6:历史；7:地理；8:政治；9:信息技术)',10
            '学段(10:小学;20:初中;30:高中;)',11
        ];
             */

            $paramsUcenter[$k] = [
                'username' => $v[0],
                'mobile' => $v[1],
                'email'=>$v[2],
                'sex' => $v[3],
                'department_id'=>$v[4],
                'idcard' => $v[5],
                'bank_name' => $v[6],
                'bank_area' => $v[7],
                'bank_deposit' => $v[8],
                'bank_account' => $v[9],
                'user_source' => $this->user_source,
                'user_type' => $allDepartment[intval($v[4])]['is_outer'],//0内部员工 1外包
                'admin_id' => $this->user['user_id'],
                'grade_part' => $v[11],
                'subject' => $v[10],
            ];
        }
        if(!empty($paramsUcenter)){
            $columns = array_keys($paramsUcenter[1]);
            $rows = [];
            foreach($paramsUcenter as $v){
                $rows[] = array_values($v);
            }
            UserCenter::batchInsertAll(UserCenter::tableName(),$columns,$rows,UserCenter::getDb());
        }else{
            array_push($error, '没有有效数据');
        }
        if (!empty($error)) {
            $error = join('----------', $error);
            throw new Exception($error, Exception::ERROR_COMMON);
        } else {
            return "导入成功";
        }

    }

    //下载格式模板
    public function Download()
    {
        $this->checkSuperAuth();
        $platformAll = Platform::findAllList();
        $platfromStr = array_map(function($v){return $v['platform_id'].':'.$v['platform_name'];},$platformAll);
        $platfromStr = join('；',$platfromStr);
        $deparmentAll = Department::findAllList();
        $deparmentStr = array_map(function($v){return $v['department_id'].':'.$v['department_name'];},$deparmentAll);
        $deparmentStr = join('；',$deparmentStr);
        $title = [
            '姓名',
            '手机号',
            '邮箱',
            '性别(1:男；2:女)',
            '所属部门('.$deparmentStr.')',
            "身份证号",
            '银行名称',
            '银行区域',
            '银行卡类型',
            '银行卡号',
            '学科(0:数学；1:语文；2:英语；3:物理；4:化学；5:生物；6:历史；7:地理；8:政治；9:信息技术)',
            '学段(10:小学;20:初中;30:高中;)',
//            '平台权限(逗号分割)('.$platfromStr.')'
        ];

        $excelData = [];
        $excelData[] = $title;

        $objPHPExcel = new \PHPExcel();
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->setTitle('error');
        $objSheet->fromArray($excelData);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="格式模版.xls"');
        header('Cache-Control: max-age=1');
        $objWriter->save('php://output');
        return '';
    }
}
