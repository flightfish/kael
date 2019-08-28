<?php
namespace common\components\mysql;

use yii\db\Command;

class MysqlCommand extends Command
{
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    protected function queryInternal($method, $fetchMode = null)
    {
        try {
            return parent::queryInternal($method, $fetchMode);
        } catch (\yii\db\Exception $e) {
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
//                echo '重连数据库';
                $this->db->close();
                $this->db->open();
                $this->pdoStatement = null;
                return parent::queryInternal($method, $fetchMode);
            }
            throw $e;
        }
    }

    public function execute()
    {
        try {
            return parent::execute();
        } catch (\yii\db\Exception $e) {
            echo $e->getMessage();
            exit('##@@has gone away');
            if ($e->errorInfo == 'null' || $e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
//                echo '重连数据库';
                $this->db->close();
                $this->db->open();
                $this->pdoStatement = null;
                return parent::execute();
            }
            throw $e;
        }
    }
}