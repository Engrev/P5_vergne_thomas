<?php
require 'vendor/autoload.php';
require 'core/defines.php';

$Loader = new Twig\Loader\FilesystemLoader(__DIR__.'/views/');
$Twig = new Twig\Environment($Loader, [
    'debug' => _DEBUG_,
    'cache' => false //__DIR__.'/cache'
]);
$Twig->addGlobal('_path_', _PATH_);
$Twig->addGlobal('_css_', []);
$Twig->addGlobal('_js_', []);
if (_DEBUG_ === true) {
    $Twig->addExtension(new Twig\Extension\DebugExtension());
}
$Session = App\Core\Session::getInstance();
$Session->write('Twig', $Twig);
$Router = new App\Core\Router($_GET['url']);

try {
    // FRONT
    $Router->get('/', 'Home#display');

    $Router->get('/derniers-articles', 'Posts#last');
    $Router->get('/articles/:id-:slug', 'Posts#list')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');

    $Router->get('/categories', 'Categories#listAll');
    $Router->get('/categories/:id-:slug', 'Categories#list')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');

    /*$Router->get('/contact', function () use ($Twig) { echo $Twig->render('front/contact.twig'); });
    $Router->get('/connexion', function () use ($Twig) { echo $Twig->render('front/login.twig'); });
    $Router->get('/inscription', function () use ($Twig) { echo $Twig->render('front/register.twig'); });*/

    // ADMIN
    /*$Router->get('/admin', function () use ($Twig) { echo $Twig->render('admin/dashboard.twig'); });
    $Router->post('/admin', 'Users#login');

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
    $Router->get('/admin/users/desactiver/:id', 'UsersAdmin#deactivate')->with('id', '[0-9]+');*/

    // EXEC
    $Router->run();
} catch (App\Core\RouterException $RouterException) {
    $RouterException->display(404);
}