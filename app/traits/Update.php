<?php
namespace app\traits;

trait Update
{
    public function update(array $updateFieldsAndValues, $table, $connection)
    {
        $fields = $updateFieldsAndValues['fields'];
        $where = $updateFieldsAndValues['where'];

        $updateFields = '';

        foreach (array_keys($fields) as $field) {
            $updateFields .= "{$field} = :{$field},";
        }

        $updateFields = rtrim($updateFields, ',');

        $whereUpdate = array_keys($where);
        $bind = array_merge($fields, $where);
        $sql = sprintf("UPDATE %s SET %s WHERE %s", $table, $updateFields, "{$whereUpdate[0]} = :{$whereUpdate[0]}");

        try {
            $prepare = $connection->prepare($sql);
            return $prepare->execute($bind);
        } catch (PDOException $e) {
            var_dump($e->getMessage);
        }
    }
}