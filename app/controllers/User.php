<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\helpers\redirect;
use app\database\models\User as ModelUser;
require_once __DIR__ . '/../helpers/redirect.php';

class User
{

    public function __construct()
    {
        $this->validate = new Validate;
        $this->user = new ModelUser;
    }

    public function create($request, $response, $args)
    {
        Flash::set('message', 'teste');
        return \app\helpers\redirect($response, '/');
    }
}