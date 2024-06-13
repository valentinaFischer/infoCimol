<?php
namespace app\traits;

use PDO;
use PDOException;

trait Read
{
    public function getUserByEmail($email) {

        try {
            $connection = $this->getConnection(); 
            $statement = $connection->prepare("
            SELECT 
            usuario.pessoa_id_pessoa, 
            pessoa.nome, 
            pessoa.cpf, 
            usuario.senha, 
            pessoa.id_pessoa, 
            MAX(CASE WHEN administrador.pessoa_id_pessoa IS NOT NULL THEN 1 ELSE 0 END) AS admin,
            MAX(CASE WHEN aluno.pessoa_id_pessoa IS NOT NULL THEN 1 ELSE 0 END) AS aluno,
            MAX(CASE WHEN professor.pessoa_id_pessoa IS NOT NULL THEN 1 ELSE 0 END) AS professor,
            MAX(CASE WHEN coord.professor_pessoa_id_pessoa IS NOT NULL THEN 1 ELSE 0 END) AS coordenador_curso
            FROM pessoa 
            LEFT JOIN usuario ON usuario.pessoa_id_pessoa = pessoa.id_pessoa 
            LEFT JOIN administrador ON administrador.pessoa_id_pessoa = pessoa.id_pessoa 
            LEFT JOIN aluno ON aluno.pessoa_id_pessoa = pessoa.id_pessoa 
            LEFT JOIN professor ON professor.pessoa_id_pessoa = pessoa.id_pessoa 
            LEFT JOIN coordenacao AS coord ON coord.professor_pessoa_id_pessoa = pessoa.id_pessoa
            WHERE pessoa.email = :email
            GROUP BY usuario.pessoa_id_pessoa, pessoa.nome, pessoa.cpf, usuario.senha, pessoa.id_pessoa
        ");
        
        $statement->bindParam(':email', $email);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
        
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function findBy($field, $value, $fetchAll = false, $table)
    {
        try {
            $connection = $this->getConnection();
            $prepared = $connection->prepare("SELECT * FROM {$table} WHERE {$field} = :{$field}");
            $prepared->bindValue(":{$field}", $value);
            $prepared->execute();
            return $fetchAll ? $prepared->fetchAll() : $prepared->fetch();
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }
}