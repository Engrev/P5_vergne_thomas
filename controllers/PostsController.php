<?php
namespace App\Controllers;
use App\Exceptions\ManagerException;

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
        $last_posts = $this->posts_manager->listLasts(10);
        $this->render('last-posts', ['head'=>['title'=>'Les derniers articles', 'meta_description'=>''], 'page'=>'derniers-articles', 'last_posts'=>$last_posts]);
    }

    /**
     * Displays a post.
     *
     * @param string $id
     * @param string $slug
     */
    public function one(string $id, string $slug)
    {
        try {
            $post = $this->posts_manager->list($id, $slug);
        } catch (ManagerException $ManagerException) {
            $ManagerException->display(404, true);
        }
        $this->render('post', ['head'=>['title'=>$post->title, 'meta_description'=>$post->description], 'post'=>$post]);
    }
}