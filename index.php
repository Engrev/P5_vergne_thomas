<?php
require 'vendor/autoload.php';

$Router = new App\Core\Router($_GET['url']);

$Router->get('/', function (){ echo 'Home.'; });
$Router->get('/articles', function (){ echo 'Afficher tout les articles.'; });
$Router->get('/articles/:id-:slug/:page', 'Posts#show')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+')->with('page', '[0-9]+');
$Router->get('/articles/:id-:slug', 'Posts#show')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');
$Router->get('/articles/:id', 'Posts#show');
//$Router->get('/articles/:id', function (){ require 'templates/form.tpl'; });
$Router->post('/articles/:id', function ($id){ echo "Afficher par POST l'article $id." . var_dump($_POST); });

$Router->run();