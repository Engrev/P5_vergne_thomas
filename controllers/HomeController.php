<?php
namespace App\Controllers;

class HomeController extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display()
    {
        $this->render('home', ['head'=>['title'=>'Accueil', 'meta_description'=>''], 'page'=>'accueil']);
    }

    public function displaySignin()
    {
        $this->render('sign-in', ['head'=>['title'=>'Connexion', 'meta_description'=>'']]);
    }

    public function displaySignup()
    {
        $this->render('sign-up', ['head'=>['title'=>'Connexion', 'meta_description'=>'']]);
    }
}