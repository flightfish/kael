<?php
namespace questionmis\modules\admin\controllers;

use common\businessmodels\WxEduCity;
use common\businessmodels\WxEduSchool;
use Yii;
use questionmis\controllers\BaseController;
use common\basedbmodels\BaseEdition;
use common\basedbmodels\BaseBook;
use questionmis\components\exception\Exception;
use yii\web\Controller;

class IndexController extends Controller{
    public $enableCsrfValidation = false;
    public $layout = 'main';
    public function actionUser()
    {
        return $this->render('user');
    }


    public function init() {
        parent::init();
    }

}