<?php
namespace Blog\Controllers;
use Blog\Core\ReCaptcha;
use Blog\Core\Validator;

/**
 * Class CommentsController
 * @package Blog\Controllers
 */
class CommentsController extends Controllers
{
    /**
     * CommentsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check and create a comment.
     *
     * @param string $id_post
     * @param string $slug
     */
    public function createComment(string $id_post, string $slug)
    {
        if ($this->issetPostSperglobal('comment') && $this->issetPostSperglobal('recaptcha_response')) {
            $User = $this->session->read('User');
            if (!is_null($User)) {
                $name = $email = null;
                $id_user = $User->getIdUser();
            } else {
                $name = trim($this->getPostSuperglobal('name'));
                $id_user = null;
                $email = trim($this->getPostSuperglobal('email'));
            }
            $comment = trim($this->getPostSuperglobal('comment'));

            $Validator = new Validator($_POST);
            $Validator->isEmail('email', "L'adresse mail doit être une adresse email valide.");
            if ($Validator->isValid()) {
                if (!empty($comment) && !empty($this->getPostSuperglobal('recaptcha_response')) && $name === null || $name !== '' && $email === null || $email !== '') {
                    $ReCaptcha = new ReCaptcha($this->getPostSuperglobal('recaptcha_response'));
                    $this->comments_manager->create($comment, $id_post, $name, $email, $id_user);
                    $this->session->writeFlash('success', "Le commentaire a été envoyé avec succès. Il devra être validé avant d'apparaître sur cette page.");
                    $this->redirect("articles/$id_post-$slug");
                } else {
                    $this->session->writeFlash('danger', "Certains champs sont vides.");
                }
            } else {
                $errors = $Validator->getErrors();
                foreach ($errors as $champs => $message) {
                    $this->session->writeFlash('danger', $message);
                }
            }
        } else {
            $this->session->writeFlash('danger', "Certains champs sont manquants.");
        }
        $_post = $this->getSpecificPost($_POST);
        $post = $this->posts_manager->display($id_post, $slug, $id_user);
        $this->render('post', ['head'=>['title'=>$post->title, 'meta_description'=>$post->description], '_post'=>isset($_post) ? $_post : '', 'post'=>$post]);
    }
}