<?php
namespace src\handlers;
use \src\models\User;
use src\models\UserRelation;

class UserHandler {

    public static function checkLogin()
    {
        if (!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];
            $data = User::select()->where('token', $token)->one();
            if (count($data) > 0) {
                $loggedUser = new User();

                $loggedUser->id = $data['id'];
                $loggedUser->name = $data['name'];
                $loggedUser->birthdate = $data['birthdate'];
                $loggedUser->avatar = $data['avatar'];
                $loggedUser->email = $data['email'];

                return $loggedUser;
            }
        } else {
            return false;
        }
    }

    public static function verifyLogin($email, $password)
    {
        $user = User::select()->where('email', $email)->one();
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $token = md5(time() . rand(0, 9999) . time());
                User::update()->set('token', $token)->where('email', $email)->execute();
                return $token;
            }
        }
            return false;
    }

    public static function emailExists($email)
    {
        $user = User::select()->where('email', $email)->one();
        return (bool) $user;
    }

    public static function idExists($id)
    {
        $user = User::select()->where('id', $id)->one();
        return (bool) $user;
    }

    public static function addUser($name, $email, $password, $birthdate)
    {
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().rand(0, 9999).time());
        User::insert([
            'name' => $name,
            'email' => $email,
          //  'password' => md5($password),
            'password' => $pass,
            'birthdate' => $birthdate,
            'token' => $token
        ])->execute();
        return $token;
    }

    public static function getUser($id, $full = false)
    {
        $data = User::select()->where('id', $id)->one();
        if ($data) {
            $user = new User();
            $user->id = $data['id'];
            $user->email = $data['email'];
            $user->name = $data['name'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];

            if ($full) {
                $user->followers = [];
                $user->followings = [];
                $user->photos = [];

                //Pegar os followers
                $followers = UserRelation::select()->where('user_to', $id)->get();
                foreach ($followers as $follower) {
                    $newData = User::select()->where('id', $follower['user_from'])->one();
                    $newUser = new User();
                    $newUser->id = $newData['id'];
                    $newUser->name = $newData['name'];
                    $newUser->avatar = $newData['avatar'];

                    $user->followers[] = $newUser;
                }

                //Pegar os followings
                $followings = UserRelation::select()->where('user_from', $id)->get();
                foreach ($followings as $following) {
                    $newData = User::select()->where('id', $following['user_to'])->one();
                    $newUser = new User();
                    $newUser->id = $newData['id'];
                    $newUser->name = $newData['name'];
                    $newUser->avatar = $newData['avatar'];

                    $user->followings[] = $newUser;
                }

                //Pegar as photos
                $user->photos = PostHandler::getPhotosFrom($id);
            }

            return $user;
        } else {
            return false;
        }
    }

    public static function isFollowing($from, $to)
    {
        $data = UserRelation::select()->where('user_from', $from)->where('user_to', $to)->one();
//        if (count($data) > 0) {
//            $data = true;
//        }
//        $data = false;
        return (bool) $data;
    }

    public static function follow($from, $to)
    {
        UserRelation::insert([
            'user_from' => $from,
            'user_to' => $to
        ])->execute();
    }

    public static function unfollow($from, $to)
    {
        UserRelation::delete()->where('user_from', $from)->where('user_to', $to)->execute();
    }

    public static function searchUsers($term)
    {
        $users = [];
        $data = User::select()->where('name', 'like', '%'.$term.'%')->get();
        if ($data) {
            foreach ($data as $user) {
                $newUser = new User();
                $newUser->id = $user['id'];
                $newUser->name = $user['name'];
                $newUser->avatar = $user['avatar'];

                $users[] = $newUser;
            }
        }
        return $users;
    }

/*    public static function updateUser($id, $name, $birthdate, $email, $city, $work, $password)
    {
        User::update()->set([
            'name' => $name,
            'birthdate' => $birthdate,
            'email' => $email,
            'city' => $city,
            'work' => $work,
        ])->where('id', $id)->execute();

        if (!empty($password)) {
            $pass = password_hash($password, PASSWORD_DEFAULT);
            User::update()->set('password', $pass)->where('id', $id)->execute();
        }
    } */

    public static function updateUser($fields, $idUser) {
        if(count($fields) > 0) {

            $update = User::update();

            foreach($fields as $fieldName => $fieldValue) {
                if($fieldName == 'password') {
                    $fieldValue = password_hash($fieldValue, PASSWORD_DEFAULT);
                }

                $update->set($fieldName, $fieldValue);
            }

            $update->where('id', $idUser)->execute();

        }
    }

}