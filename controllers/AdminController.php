<?php
namespace App\Controllers;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController extends Controllers
{
    const TEMPLATE_TYPE = 'admin';

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays admin dashboard page.
     */
    public function displayDashboard()
    {
        $this->render(self::TEMPLATE_TYPE, 'dashboard', ['head'=>['title'=>'Dashboard', 'meta_description'=>'']]);
    }
}