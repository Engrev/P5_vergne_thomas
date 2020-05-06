<?php
namespace App\Controllers;
use App\Core\Database;
use App\Core\Session;
use App\Managers\CategoriesManager;
use App\Managers\PostsManager;

class Controllers
{
    protected $twig;
    protected $categories_manager;
    protected $posts_manager;

    public function __construct()
    {
        $this->twig = Session::getInstance()->read('Twig');
        $this->categories_manager = new CategoriesManager(Database::getInstance());
        $this->posts_manager = new PostsManager(Database::getInstance());
    }

    protected function render($template, $data)
    {
        $data['categories'] = $this->categories_manager->listAll();
        $data['number_posts'] = $this->posts_manager->countPostsCategory();
        echo $this->twig->render('front/'.$template.'.twig', $data);
    }
}