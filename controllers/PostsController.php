<?php
namespace Blog\Controllers;
use Blog\Exceptions\ManagerException;

/**
 * Class PostsController
 * @package Blog\Controllers
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
            $User = $this->session->read('User');
            $id_user = !is_null($User) ? $User->getIdUser() : null;
            $post = $this->posts_manager->display($id, $slug, $id_user);
            $comments = $this->comments_manager->display($id);
            foreach ($comments as $comment) {
                $comment->date_add = $this->comments_manager->dateIntervalComments($comment->date_add);
            }
        } catch (ManagerException $ManagerException) {
            $ManagerException->display(404, true);
        }
        $this->render('post', ['head'=>['title'=>$post->title, 'meta_description'=>$post->description], 'post'=>$post, 'comments'=>$comments]);
    }

    /**
     * Check and create a post.
     */
    public function createPost()
    {
        if (isset($_POST['title'], $_POST['description'], $_POST['content'], $_POST['categorie'], $_POST['publish'])) {
            $empty_field = 0;
            foreach ($_POST as $key => $post) {
                if ($key !== 'publish' && empty(trim($post))) {
                    $empty_field++;
                }
            }
            if ($empty_field === 0) {
                $User = $this->session->read('User');
                $this->posts_manager->create($_POST, $User->getIdUser());
                $this->session->writeFlash('success', "L'article a été créé avec succès.");
                $this->redirect('dashboard');
            } else {
                $this->session->writeFlash('danger', "Certains champs sont vides.");
            }
        } else {
            $this->session->writeFlash('danger', "Certains champs sont manquants.");
        }
        $_post = $this->getPost($_POST);
        $categories = $this->categories_manager->listAll();
        $this->render('admin_post', ['head'=>['title'=>'Création d\'un article', 'meta_description'=>''], 'categories'=>$categories, '_post'=>isset($_post) ? $_post : ''], 'admin');
    }

    /**
     * Check and edit a post.
     *
     * @param int $id_post
     */
    public function editPost(int $id_post)
    {
        if (isset($_POST['title'], $_POST['description'], $_POST['content'], $_POST['categorie'], $_POST['publish'])) {
            $empty_field = 0;
            foreach ($_POST as $key => $post) {
                if ($key !== 'publish' && empty(trim($post))) {
                    $empty_field++;
                }
            }
            if ($empty_field === 0) {
                $this->posts_manager->update($_POST, $id_post);
                $this->session->writeFlash('success', "L'article a été modifié avec succès.");
                $this->redirect('dashboard');
            } else {
                $this->session->writeFlash('danger', "Certains champs sont vides.");
            }
        } else {
            $this->session->writeFlash('danger', "Certains champs sont manquants.");
        }
        $_post = $this->getPost($_POST);
        $categories = $this->categories_manager->listAll();
        $this->render('admin_post', ['head'=>['title'=>'Modification d\'un article', 'meta_description'=>''], 'categories'=>$categories, '_post'=>isset($_post) ? $_post : ''], 'admin');
    }

    /**
     * Check and delete a post.
     *
     * @param int $id_post
     */
    public function deletePost(int $id_post)
    {
        $User = $this->session->read('User');
        $post = $this->posts_manager->list($id_post);
        if ($User->getIdUser() != $post->author) {
            $this->session->writeFlash('danger', "Vous ne pouvez pas accéder à cette page.");
            $this->redirect('');
        }
        $this->posts_manager->delete($id_post);
        $this->session->writeFlash('success', "L'article a été supprimé avec succès.");
        $this->redirect('dashboard');
    }
}