<?php
namespace app\routes;

use app\controllers\User;
use app\controllers\Forum;

require '../app/middlewares/logged.php';

$app->get('/posts', Forum::class . ':start')->add($logged); 
$app->get('/posts/json', Forum::class . ':obterDadosDosPosts')->add($logged); 
$app->get('/allPosts/json', Forum::class . ':obterDadosTodosPosts')->add($logged); 
$app->get('/postar', Forum::class . ':create')->add($logged); 
$app->post('/postar', Forum::class . ':postar');
$app->get('/post/{post_id}', Forum::class . ':showPost')->add($logged); ;
$app->post('/comentar', Forum::class . ':comentar');
$app->get('/post/{post_id}/json', Forum::class . ':obterDadosDosComentarios')->add($logged); 
$app->delete('/post/delete/{post_id}', Forum::class . ':destroy');
$app->delete('/comentario/delete/{comentario_id}', Forum::class . ':delete');
$app->get('/post/edit/{post_id}', Forum::class . ':edit');
$app->put('/update/{post_id}', Forum::class . ':update');

