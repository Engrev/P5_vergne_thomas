<?php
namespace App\Controllers;
use App\Core\Database;
use App\Core\Session;
use App\Managers\CategoriesManager;
use App\Managers\PostsManager;

/**
 * Class Controllers
 * @package App\Controllers
 */
class Controllers
{
    protected $twig;
    protected $categories_manager;
    protected $posts_manager;

    /**
     * Controllers constructor.
     */
    public function __construct()
    {
        $this->twig = Session::getInstance()->read('Twig');
        $this->categories_manager = new CategoriesManager(Database::getInstance());
        $this->posts_manager = new PostsManager(Database::getInstance());
    }

    /**
     * Override Twig render() method.
     *
     * @param string $template
     * @param array  $data
     */
    protected function render(string $template, array $data)
    {
        $data['categories'] = $this->categories_manager->listAll();
        $data['number_posts'] = $this->posts_manager->countPostsCategory();
        echo $this->twig->render('front/'.$template.'.twig', $data);
    }
}