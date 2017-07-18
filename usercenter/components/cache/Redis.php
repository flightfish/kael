<?php
namespace usercenter\components\cache;

use common\libs\Constant;

class Redis extends \yii\redis\Connection
{
    public function executeCommand($name, $params = [])
    {
        $switchCatch = Constant::SWITCH_CACHE;
        if(!$switchCatch) {
            return null;
        }
        try{
            return parent::executeCommand($name,$params);
        }catch(\Exception $e){
            return null;
        }

    }
}

