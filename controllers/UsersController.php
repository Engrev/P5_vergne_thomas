<?php
namespace Blog\Controllers;
use Blog\Core\ReCaptcha;
use Blog\core\Validator;

/**
 * Class UsersController
 * @package Blog\Controllers
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
     * Creates an account.
     */
    public function createUser()
    {
        if ($this->issetPostSperglobal('lastname') &&
            $this->issetPostSperglobal('firstname') &&
            $this->issetPostSperglobal('email') &&
            $this->issetPostSperglobal('password') &&
            $this->issetPostSperglobal('password_confirm') &&
            $this->issetPostSperglobal('group'))
        {
            if (!empty($this->getPostSuperglobal('lastname')) &&
                !empty($this->getPostSuperglobal('firstname')) &&
                !empty($this->getPostSuperglobal('email')) &&
                !empty($this->getPostSuperglobal('password')) &&
                !empty($this->getPostSuperglobal('password_confirm')) &&
                !empty($this->getPostSuperglobal('group')))
            {
                $Validator = new Validator($_POST);
                $Validator->isPassword('password', "Les mot de passe ne sont pas valides.");
                $Validator->isEmail('email', "L'adresse mail doit être une adresse email valide.");
                $Validator->isConfirmed('password', "Les mot de passe sont différents.");
                if ($Validator->isValid()) {
                    $checkUserExist = $this->users_manager->checkUserExist($this->getPostSuperglobal('email'));
                    if ($checkUserExist) {
                        $this->users_manager->create($this->getPostSuperglobal('lastname'), $this->getPostSuperglobal('firstname'), $this->getPostSuperglobal('email'), $this->getPostSuperglobal('password'), $this->getPostSuperglobal('group'));
                        $this->session->writeFlash('success', "Le compte a été créé avec succès.");
                        $this->redirect('utilisateurs');
                    }
                    $this->session->writeFlash('danger', "Un compte existe déjà avec cette adresse mail.");
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
        $_post = $this->getSpecificPost($_POST);
        $this->render('admin_user', ['head'=>['title'=>'Création d\'un utilisateur', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : '']);
    }

    /**
     * Edit an account.
     *
     * @param int $id_user
     */
    public function editUser(int $id_user)
    {
        $User = $this->session->read('User');

        if ($this->issetPostSperglobal('lastname') &&
            $this->issetPostSperglobal('firstname') &&
            $this->issetPostSperglobal('email') &&
            $this->issetPostSperglobal('password') &&
            $this->issetPostSperglobal('password_confirm') &&
            $this->issetPostSperglobal('group'))
        {
            if (!empty($this->getPostSuperglobal('lastname')) &&
                !empty($this->getPostSuperglobal('firstname')) &&
                !empty($this->getPostSuperglobal('email')) &&
                !empty($this->getPostSuperglobal('group')))
            {
                $Validator = new Validator($_POST);
                $Validator->isEmail('email', "L'adresse mail doit être une adresse email valide.");
                if (!empty(trim($this->getPostSuperglobal('password')))) {
                    if (!empty(trim($this->getPostSuperglobal('password_confirm')))) {
                        $Validator->isPassword('password', "Les mot de passe ne sont pas valides.");
                        $Validator->isConfirmed('password', "Les mot de passe sont différents.");
                    } else {
                        $this->session->writeFlash('danger', "Le champ confirmation du mot de passe est vide alors que le champ mot de passe ne l'est pas.");
                    }
                } else {
                    $this->setPostSuperglobal('password', null);
                    $this->setPostSuperglobal('password_confirm', null);
                }
                if ($Validator->isValid()) {
                    $user = $User->getIdUser() == $id_user ? $User : null;
                    $is_active = $this->issetPostSperglobal('activation') ? $this->getPostSuperglobal('activation') : null;
                    $this->users_manager->update($this->getPostSuperglobal('lastname'), $this->getPostSuperglobal('firstname'), $this->getPostSuperglobal('email'), $this->getPostSuperglobal('password'), $user, $id_user, $this->getPostSuperglobal('group'), $is_active);
                    $this->session->writeFlash('success', "Le compte a été modifié avec succès.");
                    $this->redirect('utilisateurs');
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
        $_post = $this->getSpecificPost($_POST);
        $this->render('admin_user', ['head'=>['title'=>'Modification d\'un utilisateur', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : ''], 'admin');
    }

    /**
     * Delete an account.
     *
     * @param int $id_user
     */
    public function deleteUser(int $id_user)
    {
        $this->posts_manager->deletedAuthor($id_user);
        $this->users_manager->delete($id_user);
        $this->session->writeFlash('success', "Le compte a été supprimé avec succès.");
        $this->redirect('utilisateurs');
    }

    /**
     * Send an email to reset the password.
     */
    public function forgotPassword()
    {
        if ($this->issetPostSperglobal('email') && !empty($this->getPostSuperglobal('email'))) {
            if ($this->users_manager->resetToken($this->getPostSuperglobal('email'))) {
                $this->session->writeFlash('success', "Les instructions pour réinitialiser votre mot de passe vous ont été envoyées par email, vous avez 30 minutes pour le faire.");
            } else {
                $this->session->writeFlash('danger', "Aucun compte ne correspond à cet adresse mail : {$this->getPostSuperglobal('email')}.");
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
            if ($this->issetPostSperglobal('new_password') && $this->issetPostSperglobal('new_password_confirm')) {
                $Validator = new Validator($_POST);
                $Validator->isPassword('new_password', "Les mot de passe ne sont pas valides.");
                $Validator->isConfirmed('new_password', "Les mot de passe ne correspondent pas.");
                if ($Validator->isValid()) {
                    $this->users_manager->newPassword($this->getPostSuperglobal('new_password'), $id_user, true);
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
     * Connect an user.
     */
    public function signIn()
    {
        if ($this->issetPostSperglobal('email') && $this->issetPostSperglobal('password') && $this->issetPostSperglobal('recaptcha_response')) {
            if (!empty($this->getPostSuperglobal('email')) && !empty($this->getPostSuperglobal('password')) && !empty($this->getPostSuperglobal('recaptcha_response'))) {
                $ReCaptcha = new ReCaptcha($this->getPostSuperglobal('recaptcha_response'));
                $remember_me = $this->issetPostSperglobal('remember_me') ? true : false;
                $User = $this->users_manager->connect($this->getPostSuperglobal('email'), $this->getPostSuperglobal('password'), $remember_me);
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
        } else {
            $this->session->writeFlash('danger', "Certains champs sont manquants.");
        }
        $_post = $this->getSpecificPost($_POST);
        $this->render('sign-in', ['head'=>['title'=>'Connexion', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : '']);
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

        if ($this->issetPostSperglobal('compte')) {
            if ($this->issetArrayPostSuperglobal('compte', 'lastname') &&
                $this->issetArrayPostSuperglobal('compte', 'firstname') &&
                $this->issetArrayPostSuperglobal('compte', 'email') &&
                $this->issetArrayPostSuperglobal('compte', 'password') &&
                $this->issetArrayPostSuperglobal('compte', 'password_confirm'))
            {
                if (!empty($this->getArrayPostSuperglobal('compte', 'lastname')) &&
                    !empty($this->getArrayPostSuperglobal('compte', 'firstname')) &&
                    !empty($this->getArrayPostSuperglobal('compte', 'email')))
                {
                    $Validator = new Validator($this->getArrayPost('compte'));
                    $Validator->isEmail('email', "L'adresse email doit être une adresse email valide.");
                    if (!empty(trim($this->getArrayPostSuperglobal('compte', 'password')))) {
                        if (!empty(trim($this->getArrayPostSuperglobal('compte', 'password_confirm')))) {
                            $Validator->isPassword('password', "Les mot de passe ne sont pas valides.");
                            $Validator->isConfirmed('password', "Les mot de passe sont différents.");
                        } else {
                            $this->session->writeFlash('danger', "Le champ confirmation du mot de passe est vide alors que le champ mot de passe ne l'est pas.");
                        }
                    } else {
                        $this->setArrayPostSuperglobal('compte', 'password', null);
                        $this->setArrayPostSuperglobal('compte', 'password_confirm', null);
                    }
                    if ($Validator->isValid()) {
                        $this->users_manager->update(
                            $this->getArrayPostSuperglobal('compte', 'lastname'),
                            $this->getArrayPostSuperglobal('compte', 'firstname'),
                            $this->getArrayPostSuperglobal('compte', 'email'),
                            $this->getArrayPostSuperglobal('compte', 'password'),
                            $User
                        );
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
            $_post = $this->getSpecificPost($this->getArrayPost('compte'));
            $this->render('profil', ['head'=>['title'=>'Profil', 'meta_description'=>''], '_post'=>isset($_post) ? $_post : ''], 'admin');
        }

        if ($this->issetPostSperglobal('social')) {
            $empty_link = 0;
            foreach ($this->getArrayPost('social') as $link) {
                if (empty($link)) {
                    $empty_link++;
                }
            }
            if ($empty_link === count($this->getArrayPost('social')) && empty($this->getArrayFilesSuperglobal('avatar', 'name', 0))) {
                $this->session->writeFlash('danger', "Vous avez soumis le formulaire Social sans rien remplir.");
            }

            if ($empty_link < count($this->getArrayPost('social'))) {
                $this->users_manager->updateInfos($this->getArrayPost('social'), $User->getIdUser());
                $this->session->writeFlash('success', "Vos informations ont été mise à jour avec succès.");
            }

            if (!empty($this->getArrayFilesSuperglobal('avatar', 'name', 0))) {
                $files = $this->upload('avatar', $this->getArrayFiles('avatar'), $User->getIdUser());
                if (empty($files['response'])) {
                    $this->users_manager->saveUpload($files['moved_files'], $User->getIdUser());
                    $this->session->writeFlash('success', "Votre photo de profil a été mise à jour avec succès.");
                }
            }

            $this->render('profil', ['head'=>['title'=>'Profil', 'meta_description'=>'']], 'admin');
        }

        if ($this->issetPostSperglobal('delete')) {
            $id_user = intval($this->getPostSuperglobal('delete'));
            if ($id_user === $User->getIdUser()) {
                $this->posts_manager->deletedAuthor($id_user);
                $this->users_manager->delete($User->getIdUser(), $User->getEmail());
                $this->session->writeFlash('success', "Votre compte a été supprimé avec succès. Un mail de confirmation vous sera envoyé.");
                $this->redirect('accueil');
            }
            $this->session->writeFlash('danger', "Le numéro d'utilisateur pour la suppression du compte ne vous correspond pas.");
            $this->render('profil', ['head'=>['title'=>'Profil', 'meta_description'=>'']], 'admin');
        }
    }
}