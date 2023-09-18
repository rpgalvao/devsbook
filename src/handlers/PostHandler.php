<?php
namespace src\handlers;
use \src\models\Post;
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

    public static function getHomeFeed($idUser, $page)
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
        $posts =[];
        foreach ($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;
            if ($postItem['id_user'] == $idUser) {
                $newPost->mine = true;
            }

            //4. Pegar as informações adicionais do usuário que fez o post
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            //4.1 Pegar as informações de LIKE
            $newPost->likes = 0;
            $newPost->liked = false;

            //4.2 Pegar os comentários
            $newPost->comments = [];

            $posts[] = $newPost;
        }

        return [
            'posts' => $posts,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }
}