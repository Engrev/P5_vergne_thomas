<?php
require 'core/defines.php';
require 'vendor/autoload.php';

$Loader = new Twig\Loader\FilesystemLoader(__DIR__.'/views/');
$Twig = new Twig\Environment($Loader, [
    'debug' => true,
    'cache' => false //__DIR__.'/cache'
]);
$Twig->getExtension(Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');
$Twig->addGlobal('_path_', _PATH_);
if ($Twig->isDebug() === true) {
    $Twig->addExtension(new Twig\Extension\DebugExtension());
}
$Session = App\Core\Session::getInstance();
$Session->write('Twig', $Twig);
$Router = new App\Core\Router($_GET['url']);

try {
    // FRONT
    $Router->get('/', 'Home#display');

    $Router->get('/derniers-articles', 'Posts#last');
    $Router->get('/articles/:id-:slug.:ext', 'Posts#list')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+')->with('ext', '[a-z]{2,4}');

    $Router->get('/categories', 'Categories#listAll');
    $Router->get('/categories/:id-:slug', 'Categories#list')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');

    /*$Router->get('/contact', function () use ($Twig) { echo $Twig->render('front/contact.twig'); });*/
    $Router->get('/connexion', 'Home#displaySignin');
    $Router->get('/inscription', 'Home#displaySignup');
    $Router->get('/mot-de-passe-oublie', 'Users#forgotPassword');

    // ADMIN
    $Router->get('/admin/dashboard', function () use ($Twig) { echo $Twig->render('admin/dashboard.twig'); });
    $Router->post('/admin/signin', 'Users#signIn');
    $Router->post('/admin/signup', 'Users#signUp');

    /*$Router->get('/admin/articles', 'PostsAdmin#listAll');
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