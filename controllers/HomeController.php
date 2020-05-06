<?php
namespace App\Controllers;
use App\Managers\CategoriesManager;
use App\Managers\PostsManager;
use App\Core\Database;

class HomeController extends Controllers
{
    private $manager;
    private $posts_manager;
    private $number_posts;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new CategoriesManager(Database::getInstance());
        $this->posts_manager = new PostsManager(Database::getInstance());
        $this->number_posts = $this->posts_manager->countPostsCategory();
    }

    public function display()
    {
        $categories = $this->manager->listAll();
        echo $this->twig->render('front/home.twig', ['head'=>['title'=>'Accueil', 'meta_description'=>''], 'page'=>'accueil', 'categories'=>$categories, 'number_posts'=>$this->number_posts]);
    }

    public function displaySignin()
    {
        echo $this->twig->render('front/sign-in.twig', ['head'=>['title'=>'Connexion', 'meta_description'=>''], 'page'=>'connexion', 'number_posts'=>$this->number_posts]);
    }

    public function displaySignup()
    {
        echo $this->twig->render('front/sign-up.twig', ['head'=>['title'=>'Inscription', 'meta_description'=>''], 'page'=>'inscription', 'number_posts'=>$this->number_posts]);
    }
}