<?php
namespace app\middlewares;

$logged = function ($request, $handler) use ($app) {
    $response = $handler->handle($request);
    $existingContent = (string) $response->getBody();

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write($existingContent);

    if(!isset($_SESSION['is_logged_in'])){
        return \app\helpers\redirect($response, '/');
    }

    return $response;
};