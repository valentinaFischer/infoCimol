<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\controllers\Base;
use app\database\models\User;

class Home extends Base
{
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new User;
        $this->validate = new Validate;
    }

    public function index($request, $response) 
    {

        $userData = isset($_SESSION['user_logged_data']) ? $_SESSION['user_logged_data'] : null;

        $message = Flash::get('message');

        return $this->getTwig()->render($response, $this->setView('site/home'), [
            'title' => 'Home',
            'user' => $userData,
            'message' => $message
        ]);
    }
}