<?php
namespace common\models;

class BaseActiveRecord extends \yii\db\ActiveRecord {
    const STATUS_VALID = 0;//正常状态
    const STATUS_INVALID = 1;//删除状态
    const STATUS_FREE_STAUTS = 2;//空闲状态

    const SUMMARY_NOT = "NOT_ALLOW_ONLINE";


    public static function batchInsertAll($table,$columns, $rows, $db = "", $insertType = "INSERT")
    {
        if(empty($rows)){
            return 0;
        }
        empty($db) && $db = self::getDb();
        $schema = $db->getSchema();
        if (($tableSchema = $schema->getTableSchema($table)) !== null) {
            $columnSchemas = $tableSchema->columns;
        } else {
            $columnSchemas = [];
        }

        $values = [];
        foreach ($rows as $row) {
            $vs = [];
            foreach ($row as $i => $value) {
                if (isset($columns[$i], $columnSchemas[$columns[$i]]) && !is_array($value)) {
                    $value = $columnSchemas[$columns[$i]]->dbTypecast($value);
                }
                if (is_string($value)) {
                    $value = $schema->quoteValue($value);
                } elseif ($value === false) {
                    $value = 0;
                } elseif ($value === null) {
                    $value = 'NULL';
                }
                $vs[] = $value;
            }
            $values[] = '(' . implode(', ', $vs) . ')';
        }

        foreach ($columns as $i => $name) {
            $columns[$i] = $schema->quoteColumnName($name);
        }

        $sql =  $insertType.' INTO ' . $schema->quoteTableName($table)
        . ' (' . implode(', ', $columns) . ') VALUES ' . implode(', ', $values);
        return $db->createCommand($sql)->execute();
    }
}