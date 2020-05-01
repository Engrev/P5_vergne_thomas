<?php
require 'vendor/autoload.php';
require 'core/defines.php';

$Loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates/');
$Twig = new Twig\Environment($Loader, [
    'debug' => _DEBUG_,
    'cache' => false //__DIR__.'/cache'
]);
$Twig->addGlobal('_path_', _PATH_);
$Twig->addGlobal('_css_', []);
if (_DEBUG_ === true) {
    $Twig->addExtension(new Twig\Extension\DebugExtension());
}
$Router = new App\Core\Router($_GET['url']);
//$Router->get('/articles', function (){ echo 'Afficher tout les articles.'; });
//$Router->get('/articles/:id-:slug/:page', 'Posts#show')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+')->with('page', '[0-9]+');
//$Router->get('/articles/:id-:slug', 'Posts#show')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');
//$Router->get('/articles/:id', 'Posts#show');
//$Router->get('/articles/:id', function (){ require 'templates/form.tpl'; });
//$Router->post('/articles/:id', function ($id){ echo "Afficher par POST l'article $id." . var_dump($_POST); });


// FRONT
$Router->get('/', function () use ($Twig) { echo $Twig->render('front/home.twig', ['head'=>['title'=>'Accueil', 'meta_description'=>'']]); });

$Router->get('/articles', 'Posts#listAll');
$Router->get('/articles/:id-:slug', 'Posts#list')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');

$Router->get('/categories', 'Categories#listAll');
$Router->get('/categories/:id-:slug', 'Categories#list')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');

$Router->get('/connexion', 'Users#login');
$Router->get('/inscription', 'Users#register');

// ADMIN
$Router->get('/admin', function () use ($Twig) { echo $Twig->render('admin/dashboard.twig'); });

$Router->get('/admin/articles', 'PostsAdmin#listAll');
$Router->get('/admin/article/ajouter', 'PostsAdmin#create');
$Router->get('/admin/article/modifier/:id', 'PostsAdmin#update')->with('id', '[0-9]+');
$Router->get('/admin/article/supprimer/:id', 'PostsAdmin#delete')->with('id', '[0-9]+');

$Router->get('/admin/categories', 'CategoriesAdmin#listAll');
$Router->get('/admin/categories/ajouter', 'CategoriesAdmin#create');
$Router->get('/admin/categories/modifier/:id', 'CategoriesAdmin#update')->with('id', '[0-9]+');
$Router->get('/admin/categories/supprimer/:id', 'CategoriesAdmin#delete')->with('id', '[0-9]+');

$Router->get('/admin/users', 'UsersAdmin#listAll');
$Router->get('/admin/users/ajouter', 'UsersAdmin#create');
$Router->get('/admin/users/modifier/:id', 'UsersAdmin#update')->with('id', '[0-9]+');
$Router->get('/admin/users/desactiver/:id', 'UsersAdmin#deactivate')->with('id', '[0-9]+');

// EXEC
$Router->run();