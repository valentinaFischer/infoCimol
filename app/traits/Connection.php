<?php
namespace app\traits;

use PDOException;
use app\database\models\Connection as Connect;

trait Connection
{
    private $connection;

    public function getConnection() {
        try {
            if (!$this->connection) {
                $this->connection = Connect::connection(); // Chame o método estático connection() da classe Connection
            }
            return $this->connection;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}