<?php
namespace src\controllers;

use \core\Controller;
use src\handlers\UserHandler;
use src\handlers\PostHandler;
use src\models\Post;

class AjaxController extends Controller {

    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();
        if ($this->loggedUser === false) {
            header("Content-Type: application/json");
            echo json_encode(['error' => 'Usuário não está logado no sistema']);
            exit;
        }
    }

    public function like($attr)
    {
        $id = $attr['id'];
        if (PostHandler::isLiked($id, $this->loggedUser->id)) {
            PostHandler::deleteLike($id, $this->loggedUser->id);
        } else {
            PostHandler::addLike($id, $this->loggedUser->id);
        }
    }

    public function comments()
    {
        $array = ['error' => ''];
        $id = filter_input(INPUT_POST, 'id');
        $txt = filter_input(INPUT_POST, 'txt');

        if ($id && $txt) {
            PostHandler::addComment($id, $txt, $this->loggedUser->id);

            $array['link'] = '/perfil/'.$this->loggedUser->id;
            $array['avatar'] = '/media/avatars/'.$this->loggedUser->avatar;
            $array['name'] = $this->loggedUser->name;
            $array['body'] = $txt;
        }

        header("Content-Type: application/json");
        echo json_encode($array);
        exit;
    }

    public function upload()
    {
        $array = ['error' => ''];

        if (isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])){
            $photo = $_FILES['photo'];
            $maxWidth = 800;
            $maxHeight = 800;
            if (in_array($photo['type'], ['image/png', 'image/jpeg', 'image/jpg'])) {
                list($widthOrig, $heightOrig) = getimagesize($photo['tmp_name']);
                $ratio = $widthOrig / $heightOrig;
                $newWidth = $maxWidth;
                $newHeight = $maxHeight;
                $ratioMax = $maxWidth / $maxHeight;
                if ($ratioMax > $ratio) {
                    $newWidth = $newHeight * $ratio;
                } else {
                    $newHeight = $newWidth / $ratio;
                }

                $finalImage = imagecreatetruecolor($newWidth, $newHeight);
                switch ($photo['type']) {
                    case 'image/png':
                        $image = imagecreatefrompng($photo['tmp_name']);
                    break;
                    case 'image/jpg':
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($photo['tmp_name']);
                    break;
                }
                imagecopyresampled(
                    $finalImage, $image,
                    0,0,0,0,
                    $newWidth, $newHeight, $widthOrig, $heightOrig
                );
                $photoName = md5(time().rand(0,9999)).'.jpg';
                imagejpeg($finalImage, 'media/uploads/'.$photoName);

                PostHandler::addPost($this->loggedUser->id, 'photo', $photoName);
            } else {
                $array['error'] = 'O arquivo enviado não é uma imagem válida!';
            }
        } else {
            $array['error'] = 'Nenhuma imagem foi enviada!';
        }

        header("Content-Type: application/json");
        echo json_encode($array);
        exit;
    }
}