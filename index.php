<?php
require 'core/defines.php';
require 'vendor/autoload.php';

$Loader = new Twig\Loader\FilesystemLoader(__DIR__.'/views/');
$Twig = new Twig\Environment($Loader, [
    'debug' => true,
    'cache' => false //__DIR__.'/cache'
]);
$Twig->addGlobal('_path_', _PATH_);
if ($Twig->isDebug() === true) {
    $Twig->addExtension(new Twig\Extension\DebugExtension());
}
$Session = App\Core\Session::getInstance();
$Session->write('Twig', $Twig);
$Router = new App\Core\Router($_GET['url']);
$Mail = new App\core\Mail();
$Session->write('Mail', $Mail);

// FRONT
$Router->get('/', 'Home#display');

$Router->get('/derniers-articles', 'Posts#last');
$Router->get('/articles/:id-:slug', 'Posts#one')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9\.]+');

$Router->get('/categories/:id-:slug', 'Categories#redirectWithPage')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');
$Router->get('/categories/:id-:slug/:page', 'Categories#one')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+')->with('page', '[0-9]+');

$Router->get('/connexion', 'Home#displaySignin');
$Router->post('/connexion', 'Users#signIn');
$Router->get('/deconnexion', 'Users#signOut');

$Router->get('/inscription', 'Home#displaySignup');
$Router->post('/inscription', 'Users#signUp');

$Router->get('/mot-de-passe-oublie', 'Home#displayForgotPassword');
$Router->post('/mot-de-passe-oublie', 'Users#forgotPassword');

$Router->get('/reinitialisation-mot-de-passe/:id-:token', 'Home#displayResetPassword')->with('id', '[0-9]+')->with('token', '[0-9a-zA-z]{60}');
$Router->post('/reinitialisation-mot-de-passe/:id-:token', 'Users#resetPassword')->with('id', '[0-9]+')->with('token', '[0-9a-zA-z]{60}');

$Router->get('/validation-compte/:id-:token', 'Users#validAccount')->with('id', '[0-9]+')->with('token', '[0-9a-zA-z]{60}');

//$Router->get('/contact', 'Home#displayContact');

// ADMIN
$Router->get('/dashboard', 'Admin#displayDashboard');

$Router->get('/profil', 'Admin#displayProfil');
$Router->post('/profil', 'Users#profil');

$Router->get('/articles/creer', 'Admin#displayCreatePost');
$Router->post('/articles/creer', 'Posts#createPost');
$Router->get('/articles/modifier/:id', 'Admin#displayEditPost')->with('id', '[0-9]+');
$Router->post('/articles/modifier/:id', 'Posts#editPost')->with('id', '[0-9]+');
$Router->get('/articles/supprimer/:id', 'Posts#deletePost')->with('id', '[0-9]+');

$Router->get('/categories/creer', 'Admin#displayCreateCategory');
$Router->post('/categories/creer', 'Categories#createCategory');
$Router->get('/categories/modifier/:id', 'Admin#displayEditCategory')->with('id', '[0-9]+');
$Router->post('/categories/modifier/:id', 'Categories#editCategory')->with('id', '[0-9]+');
$Router->get('/categories/supprimer/:id', 'Categories#deleteCategory')->with('id', '[0-9]+');

/*$Router->get('/users', 'UsersAdmin#listAll');
$Router->get('/users/ajouter', 'UsersAdmin#create');
$Router->get('/users/modifier/:id', 'UsersAdmin#update')->with('id', '[0-9]+');
$Router->get('/users/desactiver/:id', 'UsersAdmin#deactivate')->with('id', '[0-9]+');*/

try {
    // EXEC
    $Router->run();
} catch (App\Exceptions\RouterException $RouterException) {
    $RouterException->display(404);
}