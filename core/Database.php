<?php
namespace App\Core;
use PDO;

class Database extends PDO
{
    private static $instance;
    const HOST = 'localhost';
    const LOGIN = 'root';
    const PASSWORD = '';
    const DATABASE = 'blog';

    public function __construct()
    {
        parent::__construct('mysql:dbname='.self::DATABASE.';host='.self::HOST.';charset=utf8', self::LOGIN, self::PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
    }

    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function query($query, $params = false)
    {
        if ($params) {
            $req = parent::prepare($query);
            $req->execute($params);
        } else {
            $req = parent::query($query);
        }
        return $req;
    }
}