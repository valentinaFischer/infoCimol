<?php
namespace app\helpers;

function redirect($response, $to, $status = 302)
{
    return $response->withHeader('location', $to)->withStatus(302);
}