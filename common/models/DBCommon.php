<?php
namespace common\models;


class DBCommon extends BaseActiveRecord {


    public static function batchInsertAll($table,$columns, $rows, $db = "", $insertType = "INSERT")
    {
        if(empty($rows)){
            return 0;
        }
        empty($db) && $db = static::getDb();
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

        if($insertType == 'UPDATE'){
            /**
             * INSERT into wx_user_product (id,student_id)
            VALUES
            (1708453,327),(1708452,323) ON DUPLICATE KEY UPDATE id=VALUES(id),student_id=values(student_id)
             */
            $sql =  'INSERT INTO ' . $schema->quoteTableName($table)
                . ' (' . implode(', ', $columns) . ') VALUES ' . implode(', ', $values)
                . ' ON DUPLICATE KEY UPDATE '.join(',',array_map(function($v){return "{$v}=VALUES({$v})";},$columns));
        }else{
            $sql =  $insertType.' INTO ' . $schema->quoteTableName($table)
                . ' (' . implode(', ', $columns) . ') VALUES ' . implode(', ', $values);
        }

        return $db->createCommand($sql)->execute();
    }
}