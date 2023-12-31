<?php
namespace src\controllers;

use \core\Controller;
use src\handlers\UserHandler;
use src\handlers\PostHandler;

class PostController extends Controller {

    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();
        if ($this->loggedUser === false) {
            $this->redirect('/login');
        }
    }

    public function new()
    {
        $body = filter_input(INPUT_POST, 'body');
        if ($body) {
            PostHandler::addPost($this->loggedUser->id, 'text', $body);
        }

        $this->redirect('/');
    }

    public function delete($attr = [])
    {
        if (!empty($attr['id'])) {
            $id = $attr['id'];
            PostHandler::deletePost($id, $this->loggedUser->id);
        }

        $this->redirect('/');
    }
}