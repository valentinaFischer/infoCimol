<?php
namespace app\controllers;

use PDO;
use app\classes\Flash;
use app\classes\Validate;
use app\controllers\Base;
use app\controllers\Home;
use app\traits\Connection;
use app\database\models\User;

require_once __DIR__ . '/../helpers/redirect.php';

class Forum extends Base
{
    private $user;
    private $validate;
    use Connection;

    public function __construct()
    {
        $this->user = new User;
        $this->validate = new Validate;
    }

    public function start($request, $response) 
    {
        $userData = isset($_SESSION['user_logged_data']) ? $_SESSION['user_logged_data'] : null;
        $message = Flash::get('message');

        return $this->getTwig()->render($response, $this->setView('site/forum'), [
            'title' => 'Info Support',
            'user' => $userData,
            'message' => $message,
        ]);
    }

    public function obterDadosDosPosts($request, $response)
    {
        $connection = $this->getConnection();
            
        $stmt = $connection->query("SELECT titulo, problema, tentado, created_at, post_id, nome, id_pessoa FROM posts ORDER BY created_at DESC LIMIT 10");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($posts));

        // Retornar a resposta
        return $response;
    }

    public function obterDadosTodosPosts($request, $response)
    {
        $connection = $this->getConnection();
            
        $stmt = $connection->query("SELECT titulo, problema, tentado, created_at, post_id, nome, id_pessoa FROM posts ORDER BY created_at DESC");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($posts));

        // Retornar a resposta
        return $response;
    }

    public function create($request, $response)
    {
        $userData = isset($_SESSION['user_logged_data']) ? $_SESSION['user_logged_data'] : null;
        $messages = Flash::getAll();

        return $this->getTwig()->render($response, $this->setView('site/postar'), [
            'title' => 'Info Support',
            'user' => $userData,
            'messages' => $messages,
        ]);
    }

    public function postar($request, $response)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $userData = isset($_SESSION['user_logged_data']) ? $_SESSION['user_logged_data'] : null;
        $connection = $this->getConnection();

        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $problema = filter_input(INPUT_POST, 'problema', FILTER_SANITIZE_SPECIAL_CHARS);
        $tentado = filter_input(INPUT_POST, 'tentado', FILTER_SANITIZE_SPECIAL_CHARS);
        $id_pessoa = $userData['id'];
        $nome = $userData['nome'];
        
        $this->validate->required($titulo, $problema, $tentado);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/postar');
        }

        $table = "posts";
        $dataAtual = date('Y-m-d H:i:s');

        $postado = $this->user->create(['titulo' => $titulo, 'problema' => $problema, 'tentado' => $tentado, 'id_pessoa' => $id_pessoa, 'created_at' => $dataAtual, 'nome' => $nome], $table, $connection);

        if ($postado) {
            return \app\helpers\redirect($response, '/posts');
        } else {
            Flash::set('message', 'Ocorreu um erro ao postar, tente novamente', 'danger');
            return \app\helpers\redirect($response, '/postar');
        }
    }

    public function showPost($request, $response, $args) {
        $post_id = filter_var($args['post_id'], FILTER_SANITIZE_NUMBER_INT);
        $userData = isset($_SESSION['user_logged_data']) ? $_SESSION['user_logged_data'] : null;

        $post = $this->user->findBy('post_id', $post_id, $fetchAll = 'false', $table = 'posts');
        $messages = Flash::getAll();
        
        if (!$post)
        {
            Flash::set('message', 'Este post não existe', 'danger');
            return \app\helpers\redirect($response, '/posts');
        }

        return $this->getTwig()->render($response, $this->setView('site/post'), [
            'title' => 'Info Support',
            'user' => $userData,
            'messages' => $messages,
            'post' => $post,
            'post_id' => $post_id
        ]);
    }

    public function comentar($request, $response, $args)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $userData = isset($_SESSION['user_logged_data']) ? $_SESSION['user_logged_data'] : null;

        $nome = $userData['nome'];

        $post_id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);
        date_default_timezone_set('America/Sao_Paulo');
        $userData = isset($_SESSION['user_logged_data']) ? $_SESSION['user_logged_data'] : null;
        $connection = $this->getConnection();


        $comentario = filter_input(INPUT_POST, 'comentar', FILTER_SANITIZE_STRING);
        $id_pessoa = $userData['id'];
        
        $this->validate->comentario($comentario);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/post/' . $_POST['post_id']);
        }

        $table = "comentario";
        $dataAtual = date('Y-m-d H:i:s');

        $comentado = $this->user->create(['comentario' => $comentario, 'id_pessoa' => $id_pessoa, 'created_at' => $dataAtual, 'post_id' => $post_id, 'nome' => $nome], $table, $connection);

        if ($comentado) {
            $post_id = $_POST['post_id']; 
            
            return \app\helpers\redirect($response, '/post/' . $post_id);
        } else {
            Flash::set('message', 'Ocorreu um erro ao comentar, tente novamente', 'danger');
            return \app\helpers\redirect($response, '/post/' . $post_id);
        }
        
    }

    public function obterDadosDosComentarios($request, $response, $args)
    {
        $connection = $this->getConnection();
        $post_id = $args['post_id'];
            
        $stmt = $connection->prepare("SELECT id_coment, comentario, created_at, id_pessoa, nome FROM comentario WHERE post_id = :post_id ORDER BY created_at");
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $response = $response->withHeader('Content-Type', 'application/json');
    
        $response->getBody()->write(json_encode($comentarios));
    
        // Retornar a resposta
        return $response;
    }

    public function destroy($request, $response, $args)
    {
        $id = filter_var($args['post_id'], FILTER_SANITIZE_NUMBER_INT);

        $post = $this->user->findBy('post_id', $id, $fetchAll = false, $table = 'posts');
        
        if (!$post)
        {
            Flash::set('message', 'Post não encontrado', 'danger');
            return \app\helpers\redirect($response, '/posts');
        }

        $deleted = $this->user->delete('post_id', $id, $table = 'posts');

        if ($deleted)
        {
            Flash::set('message', 'Deletado com sucesso!', 'success');
            return \app\helpers\redirect($response, '/posts');
        }

        Flash::set('message', 'Não foi possível deletar seu post', 'danger');
        return \app\helpers\redirect($response, '/posts');
   }

   public function delete($request, $response, $args)
    {
        $id = filter_var($args['comentario_id'], FILTER_SANITIZE_NUMBER_INT);

        $comentario = $this->user->findBy('id_coment', $id, $fetchAll = false, $table = 'comentario');
        
        if (!$comentario)
        {
            Flash::set('message', 'Comentário não encontrado', 'danger');
            return \app\helpers\redirect($response, '/posts');
        }

        $deleted = $this->user->delete('id_coment', $id, $table = 'comentario');

        if ($deleted)
        {
            Flash::set('message', 'Deletado com sucesso!', 'success');
            return \app\helpers\redirect($response, '/posts');
        }

        Flash::set('message', 'Não foi possível deletar seu comentário', 'danger');
        return \app\helpers\redirect($response, '/posts');
   }

   public function edit($request, $response, $args)
    {
        $id = filter_var($args['post_id'], FILTER_SANITIZE_NUMBER_INT);

        $post = $this->user->findBy('post_id', $id, $fetchAll = false, $table = 'posts');

        if (!$post)
        {
            Flash::set('message', 'Post não encontrado', 'danger');
            return \app\helpers\redirect($response, '/posts');
        }

        $messages = Flash::getAll();

        return $this->getTwig()->render($response, $this->setView('site/edit'), [
            'title' => 'Info Support',
            'post' => $post,
            'messages' => $messages
        ]);
    }

    public function update($request, $response, $args)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $connection = $this->getConnection();
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $problema = filter_input(INPUT_POST, 'problema', FILTER_SANITIZE_SPECIAL_CHARS);
        $tentado = filter_input(INPUT_POST, 'tentado', FILTER_SANITIZE_SPECIAL_CHARS);
        $id = filter_var($args['post_id'], FILTER_SANITIZE_NUMBER_INT);

        $this->validate->required($titulo, $problema, $tentado);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/post/edit/' . $id);
        }

        $table = "posts";
        $dataAtual = date('Y-m-d H:i:s');

        $updated = $this->user->update(['fields' => ['titulo' => $titulo, 'problema' => $problema, 'tentado' => $tentado, 'created_at' => $dataAtual], 'where' => ['post_id' => $id]], $table, $connection);

        if ($updated) {
            return \app\helpers\redirect($response, '/posts');
        } else {
            Flash::set('message', 'Ocorreu um erro ao atualizar, tente novamente', 'danger');
            return \app\helpers\redirect($response, '/post/edit/' . $id);
        }
    }
}
