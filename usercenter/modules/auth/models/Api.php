<?php

namespace usercenter\modules\auth\models;
use common\libs\Constant;
use common\libs\UserToken;
use common\models\Platform;
use common\models\RelateDepartmentPlatform;
use common\models\RelateUserPlatform;
use common\libs\AES;
use common\models\CommonUser;
use common\models\LogAuthUser;
use common\models\UserCenter;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;
class Api extends RequestBaseModel {


    public $where = [];
    public $where2 = [];
    public $page;
    public $pagesize;

    const SCENARIO_WHERE = "SCENARIO_WHERE";
    const SCENARIO_WHERE_PAGE = "SCENARIO_WHERE_PAGE";

    public $platform_id;


    public function scenarios()
    {
        $scenarios =  parent::scenarios();
        $scenarios[self::SCENARIO_WHERE] = ['token','where','where2'];
        $scenarios[self::SCENARIO_WHERE_PAGE] = ['token','where','page','pagesize','where2'];
        return $scenarios;
    }

    public function getPlatformId(){
        if(!empty($this->platform_id)){
            return $this->platform_id;
        }
        $sourceUrl = Yii::$app->request->referrer;
        if(empty($sourceUrl)){
            throw new Exception('权限不足，请联系系统管理员',Exception::ERROR_COMMON);
        }
        $sourceUrlArr = parse_url($sourceUrl);
        $host = $sourceUrlArr['host'];
        if(empty($host)){
            throw new Exception('权限不足，请联系系统管理员',Exception::ERROR_COMMON);
        }
        $platformInfo = Platform::findOneByHost($host);
        if(empty($platformInfo)){
            throw new Exception('权限不足，请联系管理员',Exception::ERROR_COMMON);
        }
        $this->platform_id = $platformInfo['platform_id'];
        return $this->platform_id;
    }


    public function rules()
    {
        return array_merge([
            [['where','where2'], 'safe'],
            [['page','pagesize'], 'integer'],
            [['where'], 'required','on'=>self::SCENARIO_WHERE],
            [['page','pagesize'],'required','on'=>self::SCENARIO_WHERE_PAGE]
        ],parent::rules());
    }


    public function getUserListByPlatformWhere(){
        
        $userList = UserCenter::find()
            ->where($this->where)
            ->andWhere($this->where2)
            ->andWhere(['status'=>UserCenter::STATUS_VALID])
            ->asArray(true)->all();
        $userIds = array_column($userList,'id');
        $relate = RelateUserPlatform::findListByUserPlatform($userIds,$this->getPlatformId());
        $userIds = array_column($relate,'user_id');
        $userListFitler = [];
        foreach($userList as $v){
            if(in_array($v['id'],$userIds)){
                $userListFitler[] = $v;
            }
        }

        return $userListFitler;
    }

    public function getUserListByWhere(){
        $userList = UserCenter::find()
            ->where($this->where)
            ->andWhere($this->where2)
            ->andWhere(['status'=>UserCenter::STATUS_VALID])
            ->asArray(true)->all();
        return $userList;
    }

    public function getUserListPageByPlatformWhere(){

        $this->pagesize = max($this->pagesize,1);
        $this->page = max($this->page,1);

        if(empty($this->where)){
            $relateList = RelateUserPlatform::findListByPlatformPage($this->getPlatformId(),$this->page,$this->pagesize);
            $userIds = array_column($relateList,'user_id');
            $this->where = ['id'=>$userIds];
            return $this->getUserListByWhere();
        }else{
            $userList = $this->getUserListByPlatformWhere();
            return array_slice($userList,($this->page - 1),$this->pagesize);
        }
    }
}