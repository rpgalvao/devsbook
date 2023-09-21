<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\models\User;

class ConfigController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();
        if ($this->loggedUser === false) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $user = UserHandler::getUser($this->loggedUser->id);

        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'flash' => $flash
        ]);
    }

    public function updateUser()
    {
        $name = filter_input(INPUT_POST, 'name');
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');
        $pass1 = filter_input(INPUT_POST, 'pass1');
        $pass2 = filter_input(INPUT_POST, 'pass2');

        if (!empty($birthdate)) {
            $birthdate = explode('/', $birthdate);
            if (count($birthdate) !== 3) {
                $_SESSION['flash'] = "Data de nascimento inválida. Favor digitar uma data válida";
                $this->redirect('/config');
            }
            $birthdate = $birthdate[2] . '-' . $birthdate[1] . '-' . $birthdate[0];
            if (strtotime($birthdate) === false) {
                $_SESSION['flash'] = "Data de nascimento inválida. Favor digitar uma data válida";
                $this->redirect('/config');
            }
        }

        $user = UserHandler::getUser($this->loggedUser->id);

        if (!empty($email)) {
            if (UserHandler::emailExists($email) && ($email != $user->email)) {
                $_SESSION['flash'] = "E-mail informado já existe. Favor informar outro e-mail";
                $this->redirect('/config');
            }
        }

        if (!empty($pass1)) {
            if ($pass1 === $pass2) {
                $password = $pass2;
            }
            else {
                $_SESSION['flash'] = "A senha informada não foi validada";
                $this->redirect('/config');
            }
        }

        UserHandler::updateUser($this->loggedUser->id, $name, $birthdate, $email, $city, $work, $password);

        $this->redirect('/config');
    }
}