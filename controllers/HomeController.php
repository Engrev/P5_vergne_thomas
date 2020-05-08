<?php
namespace App\Controllers;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends Controllers
{
    const TEMPLATE_TYPE = 'front';

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
        $this->render(self::TEMPLATE_TYPE, 'home', ['head'=>['title'=>'Accueil', 'meta_description'=>''], 'page'=>'accueil']);
    }

    /**
     * Displays sign-in page.
     */
    public function displaySignin()
    {
        $this->render(self::TEMPLATE_TYPE, 'sign-in', ['head'=>['title'=>'Connexion', 'meta_description'=>'']]);
    }

    /**
     * Displays sign-up page.
     */
    public function displaySignup()
    {
        $this->render(self::TEMPLATE_TYPE, 'sign-up', ['head'=>['title'=>'Inscription', 'meta_description'=>'']]);
    }

    /**
     * Displays forgot password page.
     */
    public function displayForgotPassword()
    {
        $this->render(self::TEMPLATE_TYPE, 'forgot-password', ['head'=>['title'=>'Mot de passe oublié', 'meta_description'=>'']]);
    }

    /**
     * Displays reset password page.
     */
    public function displayResetPassword()
    {
        $this->render(self::TEMPLATE_TYPE, 'reset-password', ['head'=>['title'=>'Réinitialisation de votre mot de passe', 'meta_description'=>'']]);
    }
}