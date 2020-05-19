<?php
namespace Blog\Controllers;
use Blog\Core\Session;
use Blog\Managers\CategoriesManager;
use Blog\Managers\PostsManager;
use Blog\Managers\CommentsManager;
use Blog\Managers\UsersManager;
use Blog\Core\Database;

/**
 * Class Controllers
 * @package Blog\Controllers
 */
class Controllers
{
    protected $session;
    protected $twig;
    protected $categories_manager;
    protected $posts_manager;
    protected $comments_manager;
    protected $users_manager;

    /**
     * Controllers constructor.
     */
    public function __construct()
    {
        $this->session = Session::getInstance();
        $this->twig = $this->session->read('Twig');
        $this->categories_manager = new CategoriesManager(Database::getInstance());
        $this->posts_manager = new PostsManager(Database::getInstance());
        $this->comments_manager = new CommentsManager(Database::getInstance());
        $this->users_manager = new UsersManager(Database::getInstance());
    }

    /**
     * Checks the url.
     *
     * @param string $page
     */
    protected function redirect(string $page)
    {
        $path = _PATH_.'/'.$page;
        header('Location:'.$path);
        exit();
    }

    /**
     * Override Twig render() method.
     *
     * @param string $template
     * @param array  $data
     * @param string $type
     */
    protected function render(string $template, array $data, $type = 'front')
    {
        if (is_string($template) && is_array($data)) {
            if (!empty($this->session->hasFlashes())) {
                $data['flashes'] = $this->session->readFlash();
            }
            $UserFromCookie = $this->users_manager->connectFromCookie();
            if ($UserFromCookie !== false) {
                $this->session->write('User', $UserFromCookie);
            }
            $User = $this->session->read('User');
            if (!is_null($User)) {
                $data['userL']['id'] = $User->getIdUser();
                $data['userL']['id_group'] = $User->getIdGroup();
                $data['userL']['lastname'] = $User->getLastname();
                $data['userL']['firstname'] = $User->getFirstname();
                $data['userL']['email'] = $User->getEmail();
                $data['userL']['avatar'] = $User->getAvatar();
            }
            $data['categoriesL'] = $this->categories_manager->listAll();
            $data['number_posts'] = $this->posts_manager->countPostsCategory();
            echo $this->twig->render($type.'/'.$template.'.twig', $data);
        }
    }

    /**
     * Get the values of $_POST for forms.
     *
     * @param array $_post
     *
     * @return mixed
     */
    protected function getPost(array $_post)
    {
        foreach ($_post as $key => $value) {
            if ($key !== 'recaptcha_response') {
                $posts[$key] = $value;
            }
        }
        return $posts;
    }

    /**
     * Upload pictures.
     *
     * @param string $type
     * @param array  $files
     * @param int    $id
     *
     * @return array
     */
    protected function upload(string $type, array $files, int $id)
    {
        $response = "";
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $moved_files = array();
        $folder = 'uploads';

        if (!empty($files['name'][0])) {
            $uploaded_name = $files['name'][0];
            $tmpName       = $files['tmp_name'][0];
            $error         = $files['error'][0];
            $size          = $files['size'][0];
            $ext           = strtolower(pathinfo($uploaded_name, PATHINFO_EXTENSION));
            $upload_name   = intval($id) . ".jpg";

            switch ($type) {
                case 'avatar':
                    $subfolder = $folder . DIRECTORY_SEPARATOR . 'u';
                    break;
                case 'category':
                    $subfolder = $folder . DIRECTORY_SEPARATOR . 'c';
                    break;
                case 'post':
                    $subfolder = $folder . DIRECTORY_SEPARATOR . 'p';
                    break;
                default:
                    $subfolder = $folder;
                    break;
            }
            $targetPath =  $subfolder . DIRECTORY_SEPARATOR . $upload_name;

            switch ($error) {
                case UPLOAD_ERR_OK:
                    $valid = true;
                    $moved_files = [];
                    if (!in_array($ext, array('jpg','jpeg','png'))) {
                        $valid = false;
                        $response = "Extension invalide.";
                    }
                    if ($size/1024/1024 > 10) {     // $size in bytes : 10Mio
                        $valid = false;
                        $response = "La taille des fichiers dépasse la taille maximale autorisée.";
                    }
                    if ($valid) {
                        if (!is_dir($folder)) {
                            mkdir($folder);
                        }
                        if (!is_dir($subfolder)) {
                            mkdir($subfolder, 0777, true);
                        }

                        list($width_upload, $height_upload) = getimagesize($tmpName);
                        $width = 200;
                        $height = $height_upload / $width_upload * $width;
                        switch ($ext) {
                            case 'jpg':
                            case 'jpeg':
                                $img_upload = imagecreatefromjpeg($tmpName);
                                break;
                            case 'png':
                                $img_upload = imagecreatefrompng($tmpName);
                                break;
                        }
                        $img = imagecreatetruecolor($width, $height);
                        imagecopyresampled($img, $img_upload, 0, 0, 0, 0, $width, $height, $width_upload, $height_upload);
                        imagejpeg($img, $targetPath, 100);
                        /*switch ($ext) {
                            case 'jpg':
                            case 'jpeg':
                                imagejpeg($img, $targetPath, 100);
                                break;
                            case 'png':
                                imagepng($img, $targetPath, 100);
                                break;
                        }*/
                        imagedestroy($img);

                        //move_uploaded_file($tmpName, $targetPath);
                        $moved_files['path'] = $targetPath;
                        $moved_files['name'] = $uploaded_name;
                        $moved_files['uploaded_name'] = $upload_name;
                    }
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $response = "Le fichier téléchargé dépasse la directive UPLOAD_MAX_FILESIZE dans php.ini.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $response = "Le fichier téléchargé dépasse la directive MAX_FILE_SIZE spécifiée dans le formulaire HTML.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $response = "Le fichier envoyé n'a été envoyé que partiellement.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $response = "Aucun fichier envoyé.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $response = "Absence d'un dossier temporaire. Introduit dans PHP 4.3.10 et PHP 5.0.3.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $response = "Échec de l'écriture du fichier sur le disque. Introduit en PHP 5.1.0.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $response = "Téléchargement de fichier arrêté par extension. Introduit en PHP 5.2.0.";
                    break;
                default:
                    $response = "Erreur inconnue.";
                    break;
            }
        }
        return array('moved_files'=>$moved_files, 'response'=>$response);
    }
}