<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class ProfileController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();
        if ($this->loggedUser === false) {
            $this->redirect('/login');
        }
    }

    public function index($attr = [])
    {
        $id = $this->loggedUser->id;
        if (!empty($attr)) {
            $id = $attr['id'];
        }
        $user = UserHandler::getUser($id);

        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user' => $user
        ]);
    }
}