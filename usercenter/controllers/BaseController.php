<?php
namespace questionmis\controllers;

use questionmis\components\exception\Exception;
use questionmis\components\logs\Log;
use Yii;

use yii\web\Controller;


class BaseController extends Controller
{
    public $enableCsrfValidation = false;
    protected $loadData;
    protected $response;
    public $extLog = [];
    private $_timestart;

    public function init()
    {
        $this->_timestart = intval(microtime(true) * 1000);
        $this->response = yii::$app->response;
        $this->response->format = \yii\web\Response::FORMAT_JSON;
        $this->loadData = \Yii::$app->request->post();
        $token = \Yii::$app->request->get('token','');
        empty($token) && isset($_COOKIE['token']) && $token = $_COOKIE['token'];
        if($token){
            $this->loadData['token'] = $token;
        }
        parent::init();
    }

    /**
     * 格式化正确返回值
     * @param $data
     * @return array
     */

    protected function success($data=[],$returnOri = false){
        $timeUsed = intval(microtime(true) * 1000) - $this->_timestart;
        if($returnOri){
            //返回原始数据
            Log::info([
                'timeUsed'      => $timeUsed,
            ]);
            return $data;
        }
        Log::info([
            'timeUsed'      => $timeUsed,
            'resultData'        => $data,
        ]);
        $result = [
            'code'      => 0,
            'message'   => 'successs',
            'product'   => \Yii::$app->params['product_id'],
            'requestId'   => Yii::$app->params['requestId'],
            'data'      => $data
        ];
        return $result;
    }

    protected function  error(\Exception $exception){
        $timeUsed = intval(microtime(true) * 1000) - $this->_timestart;
        Log::info([
            'timeUsed'      => $timeUsed,
            'exception'     => "true"
        ]);
        Log::error($exception);
        $data = [];
        if($exception instanceof Exception){
            $message = $exception->getMessage();
            $code = $exception->getCode();
        }elseif(YII_DEBUG){
            $message = $exception->getMessage();
            $code = Exception::SYSTEM_ERROR_CODE;
            $data = [
                'code'=>$exception->getCode(),
                'file'=>$exception->getFile(),
                'line'=>$exception->getLine(),
                'message'=>$exception->getMessage(),
                'previous'=>$exception->getPrevious(),
                'trace'=>$exception->getTraceAsString()
            ];
        }else{
            $message = "server error";
            $code = Exception::SYSTEM_ERROR_CODE;
        }
        $result = [
            'code'      => $code,
            'message'   => $message,
            'requestId'   => Yii::$app->params['requestId'],
            'product'   => \Yii::$app->params['product_id'],
            'data'      => $data
        ];
        return $result;
    }

    public function afterAction($action, $result)
    {

        return parent::afterAction($action, $result);
    }
}
