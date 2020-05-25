<?php
namespace Blog\Controllers;
use Blog\Core\ReCaptcha;

/**
 * Class FrontController
 * @package Blog\Controllers
 */
class FrontController extends Controllers
{
    /**
     * FrontController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays home page.
     */
    public function displayHome()
    {
        $this->render('home', ['head'=>['title'=>'Accueil', 'meta_description'=>''], 'page'=>'accueil']);
    }

    /**
     * Displays sign-in page.
     */
    public function displaySignin()
    {
        if (!is_null($this->session->read('User'))) {
            $this->redirect('dashboard');
        }
        $this->render('sign-in', ['head'=>['title'=>'Connexion', 'meta_description'=>'']]);
    }

    /**
     * Displays forgot password page.
     */
    public function displayForgotPassword()
    {
        $this->render('forgot-password', ['head'=>['title'=>'Mot de passe oublié', 'meta_description'=>'']]);
    }

    /**
     * Displays reset password page.
     */
    public function displayResetPassword()
    {
        $this->render('reset-password', ['head'=>['title'=>'Réinitialisation de votre mot de passe', 'meta_description'=>'']]);
    }

    /**
     * Displays contact page.
     */
    public function displayContact()
    {
        $this->render('contact', ['head'=>['title'=>'Contact', 'meta_description'=>''], 'page'=>'contact']);
    }

    /**
     * Checks and sends an email from the contact form.
     */
    public function contact()
    {
        if ($this->issetPostSperglobal('name') && $this->issetPostSperglobal('email') && $this->issetPostSperglobal('message')) {
            $empty_field = 0;
            foreach ($_POST as $key => $post) {
                if (empty(trim($post))) {
                    $empty_field++;
                }
            }
            if ($empty_field === 0) {
                $ReCaptcha = new ReCaptcha($this->getPostSuperglobal('recaptcha_response'));
                $method = __FUNCTION__;
                $this->session->read('Mail')->$method($this->getPostSuperglobal('name'), $this->getPostSuperglobal('email'), $this->getPostSuperglobal('message'));
                $this->session->writeFlash('success', "Votre message a été envoyé avec succès.");
                $this->redirect('contact');
            } else {
                $this->session->writeFlash('danger', "Certains champs sont vides.");
            }
        } else {
            $this->session->writeFlash('danger', "Certains champs sont manquants.");
        }
        $_post = $this->getSpecificPost($_POST);
        $this->render('contact', ['head'=>['title'=>'Contact', 'meta_description'=>''], 'page'=>'contact', '_post'=>isset($_post) ? $_post : '']);
    }
}