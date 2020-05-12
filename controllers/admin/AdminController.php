<?php
namespace App\Admin\Controllers;
use App\Controllers\Controllers;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController extends Controllers
{
    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Restrict access to a page on a user's id_group.
     *
     * @param int|null $id_group
     */
    public function restrict($id_group = null)
    {
        $User = $this->session->read('User');
        $restriction = !is_null($id_group) ? $id_group < $User->getIdGroup() : false;
        if (is_null($User) || !$User->getOnline() || $restriction) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                exit();
            }
            $link = array_reverse(explode('/', $_SERVER['REQUEST_URI']));
            $this->session->saveLink($link[0]);
            $this->session->writeFlash('danger', "Vous ne pouvez pas accéder à cette page.");
            $this->redirect('connexion');
        }
    }

    /**
     * Displays admin dashboard page.
     */
    public function displayDashboard()
    {
        $this->restrict(3);
        $this->render('dashboard', ['head'=>['title'=>'Tableau de bord', 'meta_description'=>''], 'page'=>'dashboard'], 'admin');
    }
}