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
        } catch (\Exception $e) {
            if (stripos($e->getMessage(),'gone away')) {
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
        } catch (\Exception $e) {
            if (stripos($e->getMessage(),'gone away')) {
//                echo '重连数据库';
                $this->db->close();
                $this->db->open();
//                $this->pdoStatement = null;
                return $this->db->pdo->exec($this->getRawSql());
//                return parent::execute();
            }
            throw $e;
        }
    }
}