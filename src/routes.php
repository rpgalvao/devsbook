<?php
use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin');
$router->get('/cadastro', 'LoginController@signup');
$router->post('/login', 'LoginController@login');
$router->post('/cadastro', 'LoginController@saveUser');
$router->post('/post/new', 'PostController@new');
$router->get('/perfil/{id}/fotos', 'ProfileController@photos');
$router->get('/perfil/{id}/follow', 'ProfileController@follow');
$router->get('/perfil/{id}/amigos', 'ProfileController@friends');
$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');
$router->get('/sair', 'LoginController@logout');
$router->get('/amigos', 'ProfileController@friends');
$router->get('/fotos', 'ProfileController@photos');
$router->get('/pesquisa', 'SearchController@index');
$router->get('/config', 'ConfigController@index');
$router->post('/config', 'ConfigController@updateUser');

//$router->get('/amigos');
//$router->get('/fotos');

