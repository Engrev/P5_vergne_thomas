<?php
namespace App\Controllers;
use App\Managers\PostsManager;
use App\Core\Database;
use App\Core\Session;

class PostsController
{
    private $manager;
    private $twig;

    public function __construct()
    {
        $this->manager = new PostsManager(Database::getInstance());
        $this->twig = Session::getInstance()->read('Twig');
    }

    public function last()
    {
        $lastPosts = $this->manager->listLasts(10);
        $numberOfPosts = $this->manager->countPostsCategory();
        echo $this->twig->render('front/last-posts.twig', ['head'=>['title'=>'Derniers articles', 'meta_description'=>''], 'page'=>'derniers-articles', 'lastPosts'=>$lastPosts, 'number_posts'=>$numberOfPosts]);
    }
}