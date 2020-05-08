<?php
namespace App\Controllers;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends Controllers
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays home page.
     */
    public function display()
    {
        $this->render('home', ['head'=>['title'=>'Accueil', 'meta_description'=>''], 'page'=>'accueil']);
    }

    /**
     * Displays sign-in page.
     */
    public function displaySignin()
    {
        $this->render('sign-in', ['head'=>['title'=>'Connexion', 'meta_description'=>'']]);
    }

    /**
     * Displays sign-up page.
     */
    public function displaySignup()
    {
        $this->render('sign-up', ['head'=>['title'=>'Inscription', 'meta_description'=>'']]);
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
}