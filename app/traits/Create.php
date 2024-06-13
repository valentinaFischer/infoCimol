<?php
namespace app\traits;

use PDOException;

trait Create 
{
    public function create(array $createFieldsAndValues, $table, $connection)
    {
        try {
            $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, implode(',', array_keys($createFieldsAndValues)), ':' . implode(',:', array_keys($createFieldsAndValues)));
            $prepared = $connection->prepare($sql);
            return $prepared->execute($createFieldsAndValues);
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }
}