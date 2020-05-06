<?php
namespace App\Controllers;
use App\Managers\PostsManager;
use App\Core\Database;

class PostsController extends Controllers
{
    private $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new PostsManager(Database::getInstance());
    }

    public function last()
    {
        $lastPosts = $this->manager->listLasts(10);
        $numberOfPosts = $this->manager->countPostsCategory();
        echo $this->twig->render('front/last-posts.twig', ['head'=>['title'=>'Les derniers articles', 'meta_description'=>''], 'page'=>'derniers-articles', 'last_posts'=>$lastPosts, 'number_posts'=>$numberOfPosts]);
    }
}