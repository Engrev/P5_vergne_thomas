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

    /**
     * Creates an account.
     */
    public function signUp()
    {
        if (isset($_POST['lastname'], $_POST['firstname'], $_POST['email'], $_POST['password'], $_POST['password_confirm'], $_POST['recaptcha_response'])) {
            if (!empty($_POST['lastname']) && !empty($_POST['firstname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirm']) && !empty($_POST['recaptcha_response'])) {
                $ReCaptcha = new ReCaptcha($_POST['recaptcha_response']);
                $Validator = new Validator($_POST);
                $Validator->isPassword('password', "Les mot de passe ne sont pas valides.");
                $Validator->isEmail('email', "L'adresse email doit être une adresse email valide.");
                $Validator->isConfirmed('password', "Les mot de passe sont différents.");
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
            $this->session->read('Mail')->validAccount($checkvalidAccount->email);
            $this->session->writeFlash('success', "Votre compte a bien été validé.");
            $this->redirect('dashboard');
        }
        $this->session->writeFlash('danger', "Ce token est invalide.");
        $this->redirect("validation-compte/{$id_user}-{$token}");
    }

    /**
     * Connect an user.
     */
    public function signIn()
    {
        if (isset($_POST['email'], $_POST['password'])) {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $remember_me = isset($_POST['remember_me']) ? true : false;
                $User = $this->users_manager->connect($_POST['email'], $_POST['password'], $remember_me);
                if (!is_string($User)) {
                    $this->session->write('User', $User);
                    $welcome = date('H') >= '18' ? 'Bonsoir ' : 'Bonjour ';
                    $this->session->writeFlash('success', $welcome.$User->getFirstname().' !');
                    if ($this->session->hasLink()) {
                        $saved_link = $this->session->readLink();
                        $this->redirect($saved_link);
                    } else {
                        $this->redirect('dashboard');
                    }
                } else {
                    $this->session->writeFlash('danger', $User);
                }
            } else {
                $this->session->writeFlash('danger', "Certains champs sont vides.");
            }
            $_post = $this->getPost($_POST);
            $this->render('sign-in', ['head'=>['title'=>'Connexion', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : '']);
        }
    }

    /**
     * Disconnect an user.
     */
    public function signOut()
    {
        $this->users_manager->disconnect();
        $this->session->writeFlash('success', "Vous êtes maintenant déconnecté.");
        $this->redirect('');
    }

    /**
     * Manages the forms on the profile page.
     */
    public function profil()
    {
        $User = $this->session->read('User');

        if (isset($_POST['compte'])) {
            if (isset($_POST['compte']['lastname'], $_POST['compte']['firstname'], $_POST['compte']['email'], $_POST['compte']['password'], $_POST['compte']['password_confirm'])) {
                if (!empty($_POST['compte']['lastname']) && !empty($_POST['compte']['firstname']) && !empty($_POST['compte']['email'])) {
                    $Validator = new Validator($_POST['compte']);
                    $Validator->isEmail('email', "L'adresse email doit être une adresse email valide.");
                    if (!empty($_POST['compte']['password'])) {
                        if (!empty($_POST['compte']['password_confirm'])) {
                            $Validator->isPassword('password', "Les mot de passe ne sont pas valides.");
                            $Validator->isConfirmed('password', "Les mot de passe sont différents.");
                        } else {
                            $this->session->writeFlash('danger', "Le champ confirmation du mot de passe est vide alors que le champ mot de passe ne l'est pas.");
                        }
                    } else {
                        $_POST['compte']['password'] = $_POST['compte']['password_confirm'] = null;
                    }
                    if ($Validator->isValid()) {
                        $this->users_manager->update($_POST['compte']['lastname'], $_POST['compte']['firstname'], $_POST['compte']['email'], $_POST['compte']['password'], $User);
                        $this->session->writeFlash('success', "Vos informations ont été mise à jour avec succès.");
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
            $_post = $this->getPost($_POST['compte']);
            $this->render('profil', ['head'=>['title'=>'Profil', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : ''], 'admin');
        }

        if (isset($_POST['social'])) {
            $empty_link = 0;
            foreach ($_POST['social'] as $link) {
                if (empty($link)) {
                    $empty_link++;
                }
            }
            if ($empty_link === count($_POST['social']) && empty($_FILES['avatar']['name'][0])) {
                $this->session->writeFlash('danger', "Vous avez soumis le formulaire Social sans rien remplir.");
            }

            if ($empty_link < count($_POST['social'])) {
                $this->users_manager->updateInfos($_POST['social'], $User->getIdUser());
                $this->session->writeFlash('success', "Vos informations ont été mise à jour avec succès.");
            }

            if (!empty($_FILES['avatar']['name'][0])) {
                $files = $this->upload('avatar', $_FILES['avatar'], $User->getIdUser());
                if (empty($files['response'])) {
                    $this->users_manager->saveUpload($files['moved_files'], $User->getIdUser());
                    $this->session->writeFlash('success', "Votre photo de profil a été mise à jour avec succès.");
                }
            }

            $this->render('profil', ['head'=>['title'=>'Profil', 'meta_description'=>'']], 'admin');
        }

        if (isset($_POST['delete'])) {
            $id_user = intval($_POST['delete']);
            if ($id_user === $User->getIdUser()) {
                //$this->posts_manager->deletedAuthor($id_user);
                //$this->users_manager->delete($User->getIdUser(), $User->getEmail());
                $this->session->writeFlash('success', "Votre compte a été supprimé avec succès. Un mail de confirmation vous sera envoyé.");
                $this->redirect('accueil');
            }
            $this->session->writeFlash('danger', "Le numéro d'utilisateur pour la suppression du compte ne vous correspond pas.");
            $this->render('profil', ['head'=>['title'=>'Profil', 'meta_description'=>'']], 'admin');
        }
    }
}