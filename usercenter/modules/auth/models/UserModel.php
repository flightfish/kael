<?php
namespace usercenter\modules\auth\models;

use Yii;
class UserModel extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }
    public function UserEdit($data){
        if(!empty($data['userid'])){
            $user=$this->findOne(['id'=>intval($data['userid'])]);
            if(isset($data['password'])&&!empty($data['password'])){
                $user->password=md5($data['password']);
            }else{
                $user->password=md5("123456");
            }

        }else{
            $user=$this;
        }
        $user->username=isset($data['username'])?$data['username']:"";
        $user->admin=isset($data['admin'])?$data['admin']:0;
        $user->mobile=$data['mobile'];
        $user->status='0';
        $user->idcard=isset($data['idcard'])?$data['idcard']:"";
        $user->sex=isset($data['sex'])?$data['sex']:"1";
        $user->bank_name=isset($data['bank_name'])?$data['bank_name']:"";
        $user->bank_deposit=isset($data['bank_deposit'])?$data['bank_deposit']:"";
        $user->bank_area=isset($data['bank_area'])?$data['bank_area']:"";
        $user->bank_account=isset($data['bank_account'])?$data['bank_account']:"";
        $user->user_type=isset($data['user_type'])?$data['user_type']:"0";
        $res = $user->save();
        return $res;
    }

    public function UserDel($userId){
        $user=$this->findOne(['id'=>intval($userId)]);
        //status 为1状态为删除
        $user->status=1;
        $res = $user->save();
        return $res;
    }
    public function UserList($table){
        $pagecount = 'select count(id) as cnt from user where 1 and status=0 ' . self::parseDatatableParam($table);
        $pagecountres = Yii::$app->get('db')->createCommand($pagecount)->queryOne();
        $list = 'select * from user where 1 and status=0' . self::parseDatatableParam($table,1);
        $listres = Yii::$app->get('db')->createCommand($list)->queryAll();
        $res['pagecountres'] = $pagecountres['cnt'];
        $res['listres'] = $listres;
        return $res;

    }
    public function CheckTel($mobile){
        $user=$this->findOne(['mobile'=>$mobile]);
        return $user;
    }

    public static function findOneById($mobile){
        $info = self::find()->where(['mobile'=>$mobile])->asArray(true)->one();
        if(empty($info)){
            return [];
        }
        return $info;
    }
    public static function findUserById($userId){
        $info = self::find()->where(['id'=>intval($userId)])->asArray(true)->one();
        if(empty($info)){
            return [];
        }
        return $info;
    }
    public function checkUser($username)
    {
        $username = $this->find()
            ->select("*")
            ->where('mobile=:mobile',[':mobile'=>$username])
            ->andWhere('status=:status',[':status'=>0])
            ->andWhere('admin=:admin',[':admin'=>1])
            ->asArray()
            ->all();
        if(!empty($username)){
            return true;
        }else{
            return false;
        }

    }



    //根据token获取userid
    public function getUserByMobile($username)
    {
        $username = $this->find()
            ->select("*")
            ->where('mobile=:mobile',[':mobile'=>$username])
            ->asArray()
            ->all();
        if(!empty($username)){
            return $username[0];
        }
        return $username;
    }
    public function checkPass($username,$password)
    {
        $password = $this->find()
            ->select("*")
            ->where('password=:password',[':password'=>$password])
            ->asArray()
            ->all();
        if(!empty($password)){
            return true;
        }else{
            return false;
        }

    }
    public function UserDetail($id){
        return $this->findOne($id);
    }
    //连接datatables传过来的条件函数
    static function parseDatatableParam($table,$status = 0)
    {
        $start = intval($table['start']);
        $len = intval($table['length']);
        $sql_limit = ' limit ' . $start . ',' . $len;
        $colLen = count($table['columns']);
        $columns = $table['columns'];
        $sql_where = '';
        $conn_arr = array();
        $sql_order = ' order by ';
        if ($colLen < 1) { // 列数不足直接返回分页数据
            return $sql_limit;
        }
        $sSearch = $table['search']['value']; // 通用搜索
        $order = $table['order'];
        foreach ($columns as $k=>$v){
            if($v['searchable']=='true'){
                if(!empty($sSearch)){
                    $conn_arr[$k]= '`'.$v['data'].'` like "%'.$sSearch.'%"';
                }
            }
            if($v['orderable']=='true'){
                foreach ($order as $kk=>$vv){
                    if($k==$vv['column']){
                        $sql_order.= $v['data'].' '.$vv['dir'];
                    }
                }
            }
        }

        if(!empty($conn_arr)){
            $sql_where = ' and (' . join(' or ', $conn_arr) . ')';
        }
        if($status>0){
            return $sql_where.$sql_order.$sql_limit;
        }else{
            return $sql_where;
        }

    }

    public function modifyPassword($studentID, $password)
    {
        $studentInfo = $this->findOne(['id' => $studentID]);
        if (empty($studentInfo)) {
            return false;
        }
        $studentInfo->password = $password;
        $ret = $studentInfo->save();
        if (!$ret) {
            $errorInfo = $this->getErrors();
            $logData = array(
                'data' => $studentInfo,
                'error' => $errorInfo,
            );
            Yii::error($logData, 'knowbox');
            return false;
        }
        return true;
    }
    /*
      * 更新passwordCode
      * @param int $userID
      * @param string $code
      * @return false 操作失败or不存在, true on succ
      *
      */
    public function updatePasswordCode($userID, $code)
    {
        $userInfo =$this->findOne(['id' => $userID]);
        if($userInfo === null){
            return -2;
        }

        $userInfo->validate_code = $code;
        $ret = $userInfo->save();//var_dump($ret); exit;
        if(!$ret){
            return false;
        }

        return true;
    }}