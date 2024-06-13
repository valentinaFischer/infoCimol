<?php
namespace app\traits;

use PDOException;


trait Delete
{
    public function delete($field, $value, $table)
    {
        try {
            $prepare = $this->connection->prepare("DELETE FROM {$table} WHERE {$field} = :{$field}");
            $prepare->bindValue($field, $value);
            return $prepare->execute();
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }
}



