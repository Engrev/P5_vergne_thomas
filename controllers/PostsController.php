<?php
namespace App\Controllers;

/**
 * Class PostsController
 * @package App\Controllers
 */
class PostsController extends Controllers
{
    /**
     * PostsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays the latest posts.
     */
    public function last()
    {
        $lastPosts = $this->posts_manager->listLasts(10);
        $this->render('last-posts', ['head'=>['title'=>'Les derniers articles', 'meta_description'=>''], 'page'=>'derniers-articles', 'last_posts'=>$lastPosts]);
    }
}