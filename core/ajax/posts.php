<?php
require 'autoload.php';
$Db = Blog\Core\Database::getInstance();
$PostsManager = new Blog\Managers\PostsManager($Db);
$data = [];
extract($_POST);

switch ($action) {
    case 'activate':
        $PostsManager->activate($published, $id_post);
        break;
}

echo json_encode($data);