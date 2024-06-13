<?php
namespace app\classes;

use app\database\models\User;


class Login {

    public function login($email, $senha)
    {
        $user = new User;

        $userFound = $user->getUserByEmail($email);

        if (!$userFound)
        {
            Flash::set('message', 'Email não cadastrado', 'danger');
            return false;
        }

        if(password_verify($senha, $userFound['senha'])) {
            $_SESSION['user_logged_data'] = [
                'email' => $email,
                'nome' => $userFound['nome'],
                'id' => $userFound['id_pessoa'],
                'perfil' => [
                    'admin' => $userFound['admin'],
                    'aluno' => $userFound['aluno'],
                    'professor' => $userFound['professor'],
                    'coordenador_curso' => $userFound['coordenador_curso']
                ]
            ];
            $_SESSION['is_logged_in'] = true;
            return true;
        } else
        {
            Flash::set('message', 'Senha incorreta', 'danger');
            return false;
        }

        return false;
    }

    public function logout()
    {
        unset($_SESSION['user_logged_data'], $_SESSION['is_logged_in']);
        session_destroy();
    }
}