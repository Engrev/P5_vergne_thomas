<?php
namespace App\Controllers;
use App\Core\ReCaptcha;
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
        } else {
            $this->session->writeFlash('danger', "L'adresse mail n'est pas ou est mal renseignée.");
        }
        $this->render('forgot-password', ['head'=>['title'=>'Mot de passe oublié', 'meta_description'=>'']]);
    }

    /**
     * Check the token and reset the password.
     *
     * @param int    $id_user
     * @param string $token
     */
    public function resetPassword(int $id_user, string $token)
    {
        $checkResetToken = $this->users_manager->checkResetPassword($id_user, $token);
        if ($checkResetToken) {
            if (isset($_POST['new_password'], $_POST['new_password_confirm'])) {
                $Validator = new Validator($_POST);
                $Validator->isPassword('new_password', "Les mot de passe ne sont pas valides.");
                $Validator->isConfirmed('new_password', "Les mot de passe ne correspondent pas.");
                if ($Validator->isValid()) {
                    $this->users_manager->newPassword($_POST['new_password'], $id_user, true);
                    $method = __FUNCTION__;
                    $this->session->read('Mail')->$method($checkResetToken->email);
                    $this->session->writeFlash('success', "Votre mot de passe a bien été réinitialisé.");
                    $this->redirect('connexion');
                } else {
                    $errors = $Validator->getErrors();
                    foreach ($errors as $champs => $message) {
                        $this->session->writeFlash('danger', $message);
                    }
                }
            }
        } else {
            $this->session->writeFlash('danger', "Ce token est invalide.");
        }
        $this->redirect("reinitialisation-mot-de-passe/{$id_user}-{$token}");
    }

    public function signUp()
    {
        if (isset($_POST['lastname'], $_POST['firstname'], $_POST['email'], $_POST['password'], $_POST['password_confirm'], $_POST['recaptcha_response'])) {
            if (!empty($_POST['lastname']) && !empty($_POST['firstname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirm']) && !empty($_POST['recaptcha_response'])) {
                $ReCaptcha = new ReCaptcha($_POST['recaptcha_response']);
                $Validator = new Validator($_POST);
                $Validator->isPassword('password', "Les mot de passe ne sont pas valides.");
                $Validator->isEmail('email', "L'adresse email doit être une adresse email valide.");
                $Validator->isConfirmed('password', "Vous devez entrer des mot de passe valides.");
                if ($Validator->isValid()) {
                    $checkUserExist = $this->users_manager->checkUserExist($_POST['email']);
                    if ($checkUserExist) {
                        $this->users_manager->create($_POST['lastname'], $_POST['firstname'], $_POST['email'], $_POST['password']);
                        $this->session->writeFlash('success', "Les instructions pour valider votre compte vous ont été envoyées par email, vous avez 30 minutes pour le faire.");
                    } else {
                        $this->session->writeFlash('danger', "Un compte existe déjà avec cette adresse email.");
                    }
                } else {
                    $errors = $Validator->getErrors();
                    foreach ($errors as $champs => $message) {
                        $this->session->writeFlash('danger', $message);
                    }
                }
            } else {
                $this->session->writeFlash('danger', "Certains champs sont vides.");
            }
        } else {
            $this->session->writeFlash('danger', "Certains champs sont manquants.");
        }
        $_post = $this->getPost($_POST);
        $this->render('sign-up', ['head'=>['title'=>'Inscription', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : '']);
    }

    /**
     * Check the token and valid an account.
     *
     * @param int    $id_user
     * @param string $token
     */
    public function validAccount(int $id_user, string $token)
    {
        $checkvalidAccount = $this->users_manager->checkValidAccount($id_user, $token);
        if ($checkvalidAccount) {
            $this->users_manager->validAccount($id_user);
            $method = __FUNCTION__;
            $this->session->read('Mail')->validAccount($_POST['email']);
            $this->session->writeFlash('success', "Votre compte a bien été validé.");
            $this->redirect('admin/dashboard');
        } else {
            $this->session->writeFlash('danger', "Ce token est invalide.");
        }
        $this->redirect("validation-compte/{$id_user}-{$token}");
    }

    private function getPost($_post)
    {
        foreach ($_post as $key => $value) {
            if ($key !== 'recaptcha_response') {
                $posts[$key] = $value;
            }
        }
        return $posts;
    }
}