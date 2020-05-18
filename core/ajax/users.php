<?php
require 'autoload.php';
$Db = App\Core\Database::getInstance();
$UsersManager = new App\Managers\UsersManager($Db);
$data = [];
extract($_POST);

switch ($action) {
    case 'activate':
        $UsersManager->activate($active, $id_user);
        break;

    case 'password_generator':
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $data['retour'] = mb_substr(str_shuffle($alphabet), 0, 8);
        break;
}

echo json_encode($data);