<?php
namespace App\Controllers;
use App\Core\Database;
use App\Core\Session;
use App\Managers\CategoriesManager;
use App\Managers\PostsManager;
use App\Managers\UsersManager;

/**
 * Class Controllers
 * @package App\Controllers
 */
class Controllers
{
    protected $session;
    protected $twig;
    protected $categories_manager;
    protected $posts_manager;
    protected $users_manager;

    /**
     * Controllers constructor.
     */
    public function __construct()
    {
        $this->session = Session::getInstance();
        $this->twig = $this->session->read('Twig');
        $this->categories_manager = new CategoriesManager(Database::getInstance());
        $this->posts_manager = new PostsManager(Database::getInstance());
        $this->users_manager = new UsersManager(Database::getInstance());
    }

    /**
     * Checks the url.
     *
     * @param string $page
     */
    protected function redirect(string $page)
    {
        $path = _PATH_.'/'.$page;
        header('Location:'.$path);
        exit();
    }

    /**
     * Override Twig render() method.
     *
     * @param string $template
     * @param array  $data
     * @param string $type
     */
    protected function render(string $template, array $data, $type = 'front')
    {
        if (is_string($template) && is_array($data)) {
            if (!empty($this->session->hasFlashes())) {
                $data['flashes'] = $this->session->readFlash();
            }
            $UserFromCookie = $this->users_manager->connectFromCookie();
            if ($UserFromCookie !== false) {
                $this->session->write('User', $UserFromCookie);
            }
            $User = $this->session->read('User');
            if (!is_null($User)) {
                $data['user'] = '';
            }
            $data['categories'] = $this->categories_manager->listAll();
            $data['number_posts'] = $this->posts_manager->countPostsCategory();
            echo $this->twig->render($type.'/'.$template.'.twig', $data);
        }
    }
}