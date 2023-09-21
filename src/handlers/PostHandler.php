<?php
namespace src\handlers;
use \src\models\Post;
use src\models\PostComment;
use src\models\PostLike;
use src\models\User;
use src\models\UserRelation;

class PostHandler {

    public static function addPost($idUser, $type, $body)
    {
        $body = trim($body);
        if ($idUser && $body) {
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public static function _postListToObject($postList, $loggedUserId)
    {
        $posts =[];
        foreach ($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;
            if ($postItem['id_user'] == $loggedUserId) {
                $newPost->mine = true;
            }

            //4. Pegar as informações adicionais do usuário que fez o post
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            //4.1 Pegar as informações de LIKE
            $likes = PostLike::select()->where('id_post', $postItem['id'])->get();

            $newPost->likeCount = count($likes);
            $newPost->liked = self::isLiked($postItem['id'], $loggedUserId);

            //4.2 Pegar os comentários
            $newPost->comments = PostComment::select()->where('id_post', $postItem['id'])->get();
            foreach ($newPost->comments as $key => $comment) {
                $newPost->comments[$key]['user'] = User::select('id, name, avatar')->where('id', $comment['id_user'])->one();
            }

            $posts[] = $newPost;
        }

        return $posts;
    }

    public static function isLiked($id, $userId)
    {
        $myLike = PostLike::select()->where('id_post', $id)->where('id_user', $userId)->get();
        /*
        if (count($myLike) > 0) {
            return true;
        } else {
            return false;
        }*/
        return (bool) count($myLike) > 0;
    }

    public static function addLike($idPost, $idUser)
    {
        PostLike::insert([
            'id_post' => $idPost,
            'id_user' => $idUser,
            'created_at' => date('Y-m-d H:i:s')
        ])->execute();
    }

    public static function deleteLike($idPost, $idUser)
    {
        PostLike::delete()->where('id_post', $idPost)->where('id_user', $idUser)->execute();
    }

    public static function addComment($id, $body, $idUser)
    {
        PostComment::insert([
            'id_post' => $id,
            'id_user' => $idUser,
            'created_at' => date('Y-m-d H:i:s'),
            'body' => $body
        ])->execute();
    }

    public static function getUserFeed($idUser, $page, $loggedUserId)
    {
        $perPage = 2;
        $postList = Post::select()->where('id_user', $idUser)->orderBy('created_at', 'desc')->page($page, $perPage)->get();

        //2.1 Pegar o total de páginas para fazer a paginação
        $totalPosts = Post::select()->where('id_user', $idUser)->count();
        $totalPages = ceil($totalPosts/$perPage);

        //3. Transformar em objetos esses posts
        $posts = self::_postListToObject($postList, $loggedUserId);

        return [
            'posts' => $posts,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }

    public static function getHomeFeed($idUser, $page): array
    {
        //1. Pegar a lista de usuários que EU sigo
        $userList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];
        foreach ($userList as $userItem) {
            $users[] = $userItem['user_to'];
        }
        $users[] = $idUser;

        //2. Pegar os posts desses usuários que eu sigo em ordem cronológica e com a quantidade de posts por página
        $perPage = 2;
        $postList = Post::select()->where('id_user', 'in', $users)->orderBy('created_at', 'desc')->page($page, $perPage)->get();

        //2.1 Pegar o total de páginas para fazer a paginação
        $totalPosts = Post::select()->where('id_user', 'in', $users)->count();
        $totalPages = ceil($totalPosts/$perPage);

        //3. Transformar em objetos esses posts
        $posts = self::_postListToObject($postList, $idUser);
        return [
            'posts' => $posts,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }

    public static function getPhotosFrom($idUser): array
    {
        $photosData = Post::select()->where('id_user', $idUser)->where('type', 'photo')->get();
        $photos = [];
        foreach ($photosData as $photo) {
            $newPost = new Post();
            $newPost->id = $photo['id'];
            $newPost->type = $photo['type'];
            $newPost->created_at = $photo['created_at'];
            $newPost->body = $photo['body'];

            $photos[] = $newPost;
        }

        return $photos;
    }
}