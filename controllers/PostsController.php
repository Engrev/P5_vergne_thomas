<?php
namespace App\Controllers;

class PostsController extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function last()
    {
        $lastPosts = $this->posts_manager->listLasts(10);
        $this->render('last-posts', ['head'=>['title'=>'Les derniers articles', 'meta_description'=>''], 'page'=>'derniers-articles', 'last_posts'=>$lastPosts]);
    }
}