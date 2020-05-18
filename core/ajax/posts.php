<?php
require 'autoload.php';
$Db = App\Core\Database::getInstance();
$PostsManager = new App\Managers\PostsManager($Db);
$data = [];
extract($_POST);

switch ($action) {
    case 'activate':
        $PostsManager->activate($published, $id_post);
        break;
}

echo json_encode($data);