<?php
namespace App\Controllers;
use App\Exceptions\ManagerException;
use App\Core\Pagination;
use App\Core\Database;

/**
 * Class CategoriesController
 * @package App\Controllers
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
    public function redirect(string $id, string $slug)
    {
        $path = _PATH_.'/categories/'.$id.'-'.$slug.'/1';
        header('Location:'.$path);
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
            $category = $this->categories_manager->list($id, $slug);
            $pagination = Pagination::pagination($page);
            $posts = $this->posts_manager->listPostsCategory($id, $pagination);
            if (!empty($posts)) {
                $nb_posts = Database::getInstance()->query("SELECT found_rows() as total")->fetch();
                $nb_pages = ceil($nb_posts->total / $pagination['limit']);
                $link_format = '<li class="page-item"><a class="page-link" href="'._PATH_.'/categories/'.$id.'-'.$slug.'/%d">%d</a></li>';
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
}