<?php
namespace common\components\mysql;

use yii\db\Connection;

class MysqlConnection extends Connection
{
    function createCommand($sql = null, $params = [])
    {
        $command = new MysqlCommand([
            'db' => $this,
            'sql' => $sql,
        ]);

        return $command->bindValues($params);
    }
}