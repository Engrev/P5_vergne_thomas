<?php
require 'autoload.php';
$Db = Blog\Core\Database::getInstance();
$CommentsManager = new Blog\Managers\CommentsManager($Db);
$data = [];
extract($_POST);

switch ($action) {
    case 'delete':
        $CommentsManager->delete($id_comment);
        break;

    case 'validate':
        $CommentsManager->validate($id_comment);
        break;
}

echo json_encode($data);