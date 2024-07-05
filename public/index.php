<?php

session_start();

require '../vendor/autoload.php';

use Dotenv\Dotenv;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use app\classes\TwigGlobal;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();

TwigGlobal::set('is_logged_in', $_SESSION['is_logged_in'] ?? '');
TwigGlobal::set('user', $_SESSION['user_logged_data'] ?? '');
TwigGlobal::set('logo_path', $_ENV['LOGO']);
TwigGlobal::set('icone_path', $_ENV['ICONE']);
TwigGlobal::set('info_path', $_ENV['INFO']);
TwigGlobal::set('cimol_path', $_ENV['CIMOL']);
TwigGlobal::set('url_path', $_ENV['URL']);

require '../app/helpers/config.php';
require '../app/helpers/redirect.php';
require '../app/routes/site.php';
require '../app/routes/user.php';

$methodOverrideMiddleware = new MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware); //para poder trabalhar com PUT e DELETE, já que o HTML não tem suporte para isso

$app->map(['GET', 'POST', 'DELETE', 'PATCH', 'PUT'], '/{routes:.+}', function ($request, $response) {
    $response->getBody()->write('Este endereço não existe');
    return $response;
});

$app->run();