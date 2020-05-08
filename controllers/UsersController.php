<?php
namespace App\Controllers;
use App\core\Validator;

/**
 * Class UsersController
 * @package App\Controllers
 */
class UsersController extends Controllers
{
    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Checks the url.
     *
     * @param string $page
     */
    public function redirect(string $page)
    {
        $path = _PATH_.'/'.$page;
        header('Location:'.$path);
        exit();
    }

    /**
     * Send an email to reset the password.
     */
    public function forgotPassword()
    {
        if ($_POST['email'] && !empty($_POST['email'])) {
            if ($this->users_manager->resetToken($_POST['email'])) {
                $this->session->writeFlash('success', "Les instructions pour réinitialiser votre mot de passe vous ont été envoyées par email, vous avez 30 minutes pour le faire.");
            } else {
                $this->session->writeFlash('danger', "Aucun compte ne correspond à cet adresse mail : {$_POST['email']}.");
            }
            $this->render('forgot-password', ['head'=>['title'=>'Mot de passe oublié', 'meta_description'=>'']]);
        } else {
            $this->session->writeFlash('danger', "L'adresse mail n'est pas ou est mal renseignée.");
            $this->render('forgot-password', ['head'=>['title'=>'Mot de passe oublié', 'meta_description'=>'']]);
        }
    }

    /**
     * Check the token and reset the password.
     *
     * @param int    $id_user
     * @param string $token
     */
    public function resetPassword(int $id_user, string $token)
    {
        $method = __FUNCTION__;
        $checkResetToken = $this->users_manager->checkResetPassword($id_user, $token);
        if ($checkResetToken) {
            if (isset($_POST['new_password'], $_POST['new_password_confirm'])) {
                if (preg_match('#^[a-zA-Z0-9_-]+$#', $_POST['new_password']) && preg_match('#^[a-zA-Z0-9_-]+$#', $_POST['new_password_confirm'])) {
                    $Validator = new Validator($_POST);
                    $Validator->isConfirmed('new_password', "Vous devez entrer des mot de passe valides.");
                    if ($Validator->isValid()) {
                        $this->users_manager->newPassword($_POST['new_password'], $id_user, true);
                        $this->session->read('Mail')->$method($checkResetToken->email);
                        $this->session->writeFlash('success', "Votre mot de passe a bien été réinitialisé.");
                        $this->redirect('connexion');
                    } else {
                        $errors = $Validator->getErrors();
                        foreach ($errors as $type => $message) {
                            $this->session->writeFlash('danger', $message);
                        }
                        $this->redirect("reinitialisation-mot-de-passe/{$id_user}-{$token}");
                    }
                } else {
                    $this->session->writeFlash('danger', "Vous devez entrer des mot de passe valides.");
                    $this->redirect("reinitialisation-mot-de-passe/{$id_user}-{$token}");
                }
            }
        } else {
            $this->session->writeFlash('danger', "Ce token est invalide.");
        }
    }
}