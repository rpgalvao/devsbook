<?php
namespace src\controllers;

use \core\Controller;
use src\handlers\LoginHandler;

class LoginController extends Controller {

    public function signin()
    {
        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('signin', [
            'flash' => $flash
        ]);
    }

    public function login()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = md5(filter_input(INPUT_POST, 'password'));

        if ($email && $password) {
            $token = LoginHandler::verifyLogin($email, $password);
            if ($token) {
                $_SESSION['token'] = $token;
                $this->redirect('/');
            } else {
                $_SESSION['flash'] = 'E-mail e/ou senha inválidos';
                $this->redirect('/login');
            }

        } else {
            $_SESSION['flash'] = 'Favor preencher todos os campos!';
            $this->redirect('/login');
        }
    }

    public function signup()
    {
        $flash = '';
        if ($_SESSION['flash']) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('signup', [
            'flash' => $flash
        ]);
    }

    public function saveUser()
    {
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = md5(filter_input(INPUT_POST, 'password'));
        $birthdate = filter_input(INPUT_POST, 'birthdate');

        if ($name && $email && $password && $birthdate) {
            $birthdate = explode('/', $birthdate);
            if (count($birthdate) !== 3) {
                $_SESSION['flash'] = "Data de nascimento inválida. Favor digitar uma data válida";
                $this->redirect('/cadastro');
            }
            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
            if (strtotime($birthdate) === false) {
                $_SESSION['flash'] = "Data de nascimento inválida. Favor digitar uma data válida";
                $this->redirect('/cadastro');
            }

            if (LoginHandler::emailExists($email) === false) {
                $token = LoginHandler::addUser($name, $email, $password, $birthdate);
                $_SESSION['token'] = $token;
                $this->redirect('/');
            } else {
                $_SESSION['flash'] = "O e-mail informado já existe. Favor usar outro endereço de e-mail para cadastro!";
                $this->redirect('/cadastro');
            }

        } else {
            $_SESSION['flash'] = 'Favor preencher todos os campos!';
            $this->redirect('/cadastro');
        }
    }
}