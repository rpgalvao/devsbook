<?php
use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin');
$router->get('/cadastro', 'LoginController@signup');
$router->post('/login', 'LoginController@login');
$router->post('/cadastro', 'LoginController@saveUser');
$router->post('/post/new', 'PostController@new');
$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');

//$router->get('/pesquisa');
//$router->get('/sair');
//$router->get('/amigos');
//$router->get('/fotos');
//$router->get('/config');
