<?php
namespace App\Admin\Controllers;
use App\Controllers\Controllers;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController extends Controllers
{
    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Restrict access to a page on a user's id_group.
     *
     * @param int|null $id_group
     */
    public function restrict($id_group = null)
    {
        $User = $this->session->read('User');
        if (!is_null($User)) {
            $restriction = !is_null($id_group) ? $id_group < $User->getIdGroup() : false;
        }
        if (is_null($User) || !$User->getOnline() || $restriction) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                exit();
            }
            $link = array_reverse(explode('/', $_SERVER['REQUEST_URI']));
            $this->session->saveLink($link[0]);
            $this->session->writeFlash('danger', "Vous ne pouvez pas accéder à cette page.");
            $this->redirect('connexion');
        }
    }

    /**
     * Displays admin dashboard page.
     */
    public function displayDashboard()
    {
        $this->restrict(3);
        $User = $this->session->read('User');
        $posts = $this->posts_manager->listAll($User->getIdUser());
        $categories = $this->categories_manager->listAll();
        $this->render('dashboard', ['head'=>['title'=>'Tableau de bord', 'meta_description'=>''], 'page'=>'dashboard', 'posts'=>$posts, 'categories'=>$categories], 'admin');
    }

    /**
     * Displays admin profil page.
     */
    public function displayProfil()
    {
        $this->restrict(3);
        $User = $this->session->read('User');
        $infos = $this->users_manager->getInfos($User->getIdUser());
        $this->render('profil', ['head'=>['title'=>'Profil', 'meta_description'=>''], 'page'=>'profil', 'user_social'=>$infos], 'admin');
    }

    /**
     * Displays admin post page.
     */
    public function displayCreatePost()
    {
        $this->restrict();
        $categories = $this->categories_manager->listAll();
        $this->render('admin_post', ['head'=>['title'=>'Création d\'un article', 'meta_description'=>''], 'categories'=>$categories], 'admin');
    }

    /**
     * Displays admin post page.
     */
    public function displayEditPost($id_post)
    {
        $this->restrict();
        $User = $this->session->read('User');
        $post = $this->posts_manager->list($id_post);
        if ($User->getIdUser() != $post->author) {
            $this->session->writeFlash('danger', "Vous ne pouvez pas accéder à cette page.");
            $this->redirect('');
        }
        $categories = $this->categories_manager->listAll();
        $this->render('admin_post', ['head'=>['title'=>'Modification d\'un article', 'meta_description'=>''], 'categories'=>$categories, 'post'=>$post], 'admin');
    }

    /**
     * Displays admin category page.
     */
    public function displayCreateCategory()
    {
        $this->render('add_category', ['head'=>['title'=>'Création d\'une catégorie', 'meta_description'=>'']], 'admin');
    }

    /**
     * Displays admin category page.
     */
    public function displayEditCategory()
    {
        $this->render('edit_category', ['head'=>['title'=>'Modification d\'une catégorie', 'meta_description'=>'']], 'admin');
    }
}