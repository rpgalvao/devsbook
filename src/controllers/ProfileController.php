<?php
namespace src\controllers;

use \core\Controller;
use src\handlers\PostHandler;
use \src\handlers\UserHandler;
use src\models\Post;
use src\models\User;

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
        $page = intval(filter_input(INPUT_GET, 'page'));
        $id = $this->loggedUser->id;
        if (!empty($attr)) {
            $id = $attr['id'];
        }
        $user = UserHandler::getUser($id, true);
        if (!$user) {
            $this->redirect('/');
        }
        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;
        $feed = PostHandler::getUserFeed($id, $page, $this->loggedUser->id);
        $isFollowing = false;
        if ($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }
        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
    }

    public function follow($attr)
    {
        $to = intval($attr['id']);
        $exists = UserHandler::idExists($to);
        if ($exists) {
            if (UserHandler::isFollowing($this->loggedUser->id, $to)) {
                UserHandler::unfollow($this->loggedUser->id, $to);
            } else {
                UserHandler::follow($this->loggedUser->id, $to);
            }
        } else {
            $this->redirect('/perfil/'.$to);
        }
        $this->redirect('/perfil/'.$to);
    }

    public function friends($attr = [])
    {
        $id = $this->loggedUser->id;
        if (!empty($attr)) {
            $id = $attr['id'];
        }
        $user = UserHandler::getUser($id, true);
        if (!$user) {
            $this->redirect('/');
        }
        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;
        $isFollowing = false;
        if ($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }
        $this->render('profile_friends', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }

    public function photos($attr = [])
    {
        $id = $this->loggedUser->id;
        if (!empty($attr)) {
            $id = $attr['id'];
        }
        $user = UserHandler::getUser($id, true);
        if (!$user) {
            $this->redirect('/');
        }
        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;
        $isFollowing = false;
        if ($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }
        $this->render('profile_photos', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }
}