<?php
namespace App\Controllers;
use App\Core\Session;

class Controllers
{
    protected $twig;

    public function __construct()
    {
        $this->twig = Session::getInstance()->read('Twig');
    }
}