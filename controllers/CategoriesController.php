<?php
namespace Blog\Controllers;
use Blog\Exceptions\ManagerException;
use Blog\Core\Pagination;
use Blog\Core\Database;

/**
 * Class CategoriesController
 * @package Blog\Controllers
 */
class CategoriesController extends Controllers
{
    /**
     * CategoriesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Redirects to the correct url.
     *
     * @param string $id
     * @param string $slug
     */
    public function redirectWithPage(string $id, string $slug)
    {
        $path = _PATH_ . '/categories/' . $id . '-' . $slug . '/1';
        header('Location:' . $path);
        exit();
    }

    /**
     * Displays a category with his posts.
     *
     * @param string $id
     * @param string $slug
     * @param int    $page
     */
    public function one(string $id, string $slug, int $page)
    {
        try {
            $category = $this->categories_manager->display($id, $slug);
            $pagination = Pagination::pagination($page);
            $posts = $this->posts_manager->listPostsCategory($id, $pagination);
            if (!empty($posts)) {
                $nb_posts = Database::getInstance()->query("SELECT found_rows() as total")->fetch();
                $nb_pages = ceil($nb_posts->total / $pagination['limit']);
                $link_format = '<li class="page-item"><a class="page-link" href="' . _PATH_ . '/categories/' . $id . '-' . $slug . '/%d">%d</a></li>';
                $page_format = '<li class="page-item active"><a class="page-link" href="javascript:void(0);">%d</a></li>';
                $ellipsis = '<li class="page-item"><a class="page-link" href="javascript:void(0);">&hellip;</a></li>';
            }
            if (isset($nb_posts) && $nb_posts->total >= $pagination['limit']) {
                $pagination_create = Pagination::create($page, $nb_pages, 1, $link_format, $page_format, $ellipsis);
            }
        } catch (ManagerException $ManagerException) {
            $ManagerException->display(404, true);
        }
        $this->render('category', ['head'=>['title'=>$category->name, 'meta_description'=>''], 'page'=>'categorie', 'category'=>$category, 'posts'=>$posts, 'pagination_create'=>isset($pagination_create) ? $pagination_create : '']);
    }

    /**
     * Check and create a category.
     */
    public function createCategory()
    {
        if (isset($_POST['name'])) {
            $name = trim($_POST['name']);
            if (!empty($name)) {
                $this->categories_manager->create($name);
                $this->session->writeFlash('success', "La catégorie a été créée avec succès.");
                $this->redirect('dashboard');
            } else {
                $this->session->writeFlash('danger', "Certains champs sont vides.");
            }
        } else {
            $this->session->writeFlash('danger', "Certains champs sont manquants.");
        }
        $_post = $this->getPost($_POST);
        $this->render('admin_category', ['head'=>['title'=>'Création d\'une catégorie', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : ''], 'admin');
    }

    /**
     * Check and edit a category.
     *
     * @param int $id_category
     */
    public function editCategory(int $id_category)
    {
        if ($id_category != 1) {
            if (isset($_POST['name'])) {
                $name = trim($_POST['name']);
                if (!empty($name)) {
                    $this->categories_manager->update($name, $id_category);
                    $this->session->writeFlash('success', "La catégorie a été modifiée avec succès.");
                    $this->redirect('dashboard');
                } else {
                    $this->session->writeFlash('danger', "Certains champs sont vides.");
                }
            } else {
                $this->session->writeFlash('danger', "Certains champs sont manquants.");
            }
            $_post = $this->getPost($_POST);
            $this->render('admin_category', ['head'=>['title'=>'Modification d\'une catégorie', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : ''], 'admin');
        } else {
            $this->session->writeFlash('danger', "Cette catégorie ne peut pas être modifiée.");
            $this->redirect('dashboard');
        }
    }

    /**
     * Check and delete a category.
     *
     * @param int $id_category
     */
    public function deleteCategory(int $id_category)
    {
        if ($id_category != 1) {
            $User = $this->session->read('User');
            if ($User->getIdGroup() != 1) {
                $this->session->writeFlash('danger', "Vous ne pouvez pas accéder à cette page.");
                $this->redirect('');
            }
            $this->posts_manager->deletedCategory($id_category);
            $this->categories_manager->delete($id_category);
            $this->session->writeFlash('success', "La catégorie a été supprimée avec succès.");
        } else {
            $this->session->writeFlash('danger', "Cette catégorie ne peut pas être supprimée.");
        }
        $this->redirect('dashboard');
    }
}