<?php

namespace questionmis\modules\admin\models;

use common\models\CommonModulesUser;
use common\models\Department;
use common\models\Platform;
use common\models\RelateUserPlatform;
use common\models\UserCenter;
use questionmis\components\exception\Exception;
use questionmis\models\RequestBaseModel;
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
    public $page = 1;
    public $pagesize = 20;
    public $data = [];
    public $id;
    public $type;
    public $mobile;
    public $filter = [];
    public $user_source = "";
    public $user_type = "1";
    public $is_admin = "0";

    public function rules()
    {
        return array_merge([
            [['page', 'pagesize', 'id'], 'integer'],
            [['type', 'mobile'], 'string'],
            [['data', 'id', 'type'], 'required', 'on' => self::SCENARIO_EDIT],
            [['type'], 'required', 'on' => self::SCENARIO_USER_UPLOAD],
            [['mobile', 'type'], 'required', 'on' => self::SCENARIO_USER_DOWNLOAD],
            [['mobile', 'type'], 'required', 'on' => self::SCENARIO_CHECKMOBILE],
            [['id', 'type'], 'required', 'on' => self::SCENARIO_DEL],
            [['page', 'pagesize', 'type'], 'required', 'on' => self::SCENARIO_LIST],
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
        if (empty($userInfo)) {
            return false;
        }
        return $userInfo;
    }


    public function checkUserAuth()
    {
        return true;
    }

    public function roleList()
    {

        $platList = Platform::findAllList();
        return $platList;
    }

    public function platformList()
    {

        $platList = Platform::findAllList();
        return $platList;
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
        $userList = UserCenter::findUserSearch($this->page,$this->pagesize,$search);
        $total  = UserCenter::findUserSearchCount($search);
        //部门 权限
        $departmentIds = array_column($userList,'department_id');
        $userIds = array_column($userList,'id');
        $platformList = RelateUserPlatform::findListByUserPlatform($userIds);
        $platformIds = array_column($platformList,'platform_id');
        //实体
        $departmentEntity  = Department::findListById($departmentIds);
        $platformEntity = Platform::findListById($platformIds);
        //拼装
        foreach($userList as $k=>$v){
            $v['platform_name'] = "";
            $v['department'] = isset($departmentEntity[$v['department_id']]) ? $departmentEntity[$v['department_id']] : "未知";
            $userList[$k] = $v;
        }
        foreach($platformList as $v){
            if(empty($platformEntity[$v['platform_id']])){
                continue;
            }
            $platformInfo = $platformEntity[$v['platform_id']];
            !empty($userList[$v['user_id']]['platform_name']) && $userList[$v['user_id']]['platform_name'] .= ',';
            $userList[$v['user_id']]['platform_name'] .= $platformInfo['platform_name'];
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
        UserCenter::updateAll(['status'=>UserCenter::STATUS_VALID],['id' => $this->id]);
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
        if (empty($this->data['center']) || empty($this->data['center']['mobile'])) {
            throw new Exception('手机号不能为空', Exception::ERROR_COMMON);
        }
        $this->user_source = $this->type;
        $this->data['center']['user_source'] = $this->user_source;
        $this->data['center']['user_type'] = $this->user_type;
        $this->data['center']['admin'] = $this->is_admin;
        $this->checkUserAuth();
        $this->data['center']['password'] = !empty($this->data['center']['password']) ? md5($this->data['center']['password']) : "";
        if (empty($this->data['center']['password']))
            unset($this->data['center']['password']);

        if (0 == $this->id) {
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
            $ret = UserCenter::updateAll($this->data['center'], ['id' => $this->id]);
            RelateUserPlatform::updateAll(['status'=>RelateUserPlatform::STATUS_VALID],['id' => $this->id]);
            RelateUserPlatform::batchAdd($this->id,$this->data['platform_list']);
        }
        return $ret;
    }

    function CommonAddUser($params, $data, $roleId)
    {

        $MobileOnly = UserCenter::findByMobile($params['mobile']);
        if (!empty($MobileOnly)) {
            throw new Exception(Exception::MOBILE_NOT_ONLY, Exception::ERROR_COMMON);
        }
        $userId = UserCenter::addUser($params);
        if (empty($userId)) {
            throw new Exception(Exception::ADD_USER_FILE, Exception::ERROR_COMMON);
        }
        RelateUserPlatform::batchAdd($userId,$roleId);
        return $userId;
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
        $paramsAuthUser = [];
        $userRoleList = [];
  //$title = ['名字', '电话', '性别(1:男；2:女)', '身份证号', '银行名称', '银行卡号', '银行区域', '银行卡类型', '学科(0:数学;1:语文;2:英语;3:物理;4:化学;5:生物;6:历史;7:地理;8:政治;9:信息技术;)', '学段(10:小学;20:初中;30:高中;)', '角色(1:管理员;2:试卷管理员;3:教辅管理员;4:知识点管理)'];

        foreach ($data as $k => $v) {
            if ($k == 0) {
                continue;//标题行
            }
            $v = array_map('strval',$v);
            $v = array_map('trim',$v);

            if ($v[0] == '0000') {
                break;
            }
            if (empty($v[0])) {
                array_push($error, '第' . ($k + 1) . '行用户名不存在');
                continue;
            }
            if ($v[9] < -1) {
                array_push($error, '第' . ($k + 1) . '行学段不存在');
                continue;
            }
            if ($v[8] < -1) {
                array_push($error, '第' . ($k + 1) . '行学科不存在');
                continue;
            }
            if (empty($v[1])) {
                array_push($error, '第' . ($k + 1) . '行，电话号码不能为空');
                continue;
            }
            if (empty($v[10])) {
                array_push($error, '第' . ($k + 1) . '行，角色不能为空');
                continue;
            }

            $MobileOnly = UserCenter::findByMobile($v[1]);
            if (!empty($MobileOnly)) {
                array_push($error, '第' . ($k + 1) . '行，电话号码已存在不能重复添加');
                continue;
            }
            $this->user_source = "admin";
            $paramsUcenter[$k] = [
                'name' => $v[0],
                'mobile' => $v[1],
                'sex' => $v[2],
                'idcard' => $v[3],
                'bank_name' => $v[4],
                'bank_deposit' => $v[5],
                'bank_area' => $v[6],
                'bank_account' => $v[7],
                'user_source' => $this->user_source,
                'user_type' => $this->user_type,
                'adminId' => $this->is_admin,
                'grade_part' => $v[9],
                'subject' => $v[8],
            ];
            $paramsAuthUser[$k] = [];
            $userRoleList[$k] = explode(',', $v[10]);
        }
        foreach ($paramsUcenter as $k => $v) {
            $this->CommonAddUser($paramsUcenter[$k], $paramsAuthUser[$k], $userRoleList[$k]);
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
        if (empty($this->mobile)) {
            throw new Exception('手机号不能为空', Exception::ERROR_COMMON);
        }
        $this->checkUserAuth();
        $this->mobile = explode("\r\n", trim($this->mobile));
        $this->mobile = array_map('trim',$this->mobile);
        $this->mobile = array_unique($this->mobile);
        usort($this->mobile,function($a,$b){if($a==$b){return 0;}return $a>$b ? 1 : -1;});
        $platformAll = Platform::findAllList();
        $platfromStr = array_map(function($v){return $v['platform_id'].':'.$v['platform_name'];},$platformAll);
        $platfromStr = join(';',$platfromStr);
        $title = [
            '名字',
            '电话',
            '性别(1:男；2:女)',
            '身份证号',
            '银行名称',
            '银行卡号',
            '银行区域',
            '银行卡类型',
            '学科(0:数学;1:语文;2:英语;3:物理;4:化学;5:生物;6:历史;7:地理;8:政治;9:信息技术;)',
            '学段(10:小学;20:初中;30:高中;)',
            '平台权限(逗号分割)('.$platfromStr.')'
        ];
        $this->user_source = $this->type;

        $userList = UserCenter::find()
            ->select('id,username,mobile,sex,idcard,bank_name,bank_deposit,bank_area,bank_account,subject,grade_part')->where(['status' => UserCenter::STATUS_VALID])
            ->andWhere(['in', 'mobile', $this->mobile])->orderBy('mobile')
            ->indexBy('mobile')
            ->asArray()->all();
        //权限
        $userIds = array_column($userList,'id');
        $platfromList = RelateUserPlatform::findListByUserPlatform($userIds);
        $platfromListIndex = [];
        foreach($platfromList as $v){
            !isset($platfromList[$v['user_id']]) && $platfromList[$v['user_id']] = [];
            $platfromListIndex[$v['user_id']][] = $v['platform_id'];
        }

        $excelData = [];
        $excelData[] = $title;
        $oldData = [];
        foreach($this->mobile as $mobile){
            if(isset($userList[$mobile])){
                $row = [
                    $userList[$mobile]['username'],
                    strval($userList[$mobile]['mobile']),
                    strval($userList[$mobile]['sex']),
                    strval($userList[$mobile]['idcard']),
                    $userList[$mobile]['bank_name'],
                    $userList[$mobile]['bank_account'],
                    $userList[$mobile]['bank_area'],
                    $userList[$mobile]['bank_deposit'],
                    strval($userList[$mobile]['subject']),
                    strval($userList[$mobile]['grade_part']),
                    isset($platfromListIndex[$userList[$mobile]['id']]) ? join(',',$platfromListIndex[$userList[$mobile]['id']]) : "",
                ];

                $oldData[] = $row;

            }else{
                $row = [
                    "",
                    $mobile,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                ];
                $excelData[] = $row;
            }
        }
        $excelData[] = [
            "0000",
            "以下用户已存在不能重复添加",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
        ];
        $excelData = array_merge($excelData,$oldData);

        $objPHPExcel = new \PHPExcel();
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->setTitle('error');
        $objSheet->fromArray($excelData);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="user.xls"');
        header('Cache-Control: max-age=1');
        $objWriter->save('php://output');
        return '';
    }
}
