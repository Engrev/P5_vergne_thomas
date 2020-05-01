<?php
namespace App\Controllers;
use App\Managers\CategoriesManager;
use App\Managers\PostsManager;
use App\Core\Database;
use App\Core\Session;

class HomeController
{
    private $manager;
    private $posts_manager;
    private $twig;

    public function __construct()
    {
        $this->manager = new CategoriesManager(Database::getInstance());
        $this->posts_manager = new PostsManager(Database::getInstance());
        $this->twig = Session::getInstance()->read('Twig');
    }

    public function display()
    {
        $categories = $this->manager->listAll();
        $numberOfPosts = $this->posts_manager->countPostsCategory();
        echo $this->twig->render('front/home.twig', ['head'=>['title'=>'Accueil', 'meta_description'=>''], 'page'=>'accueil', 'categories'=>$categories, 'number_posts'=>$numberOfPosts]);
    }
}