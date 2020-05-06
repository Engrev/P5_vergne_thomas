<?php
namespace App\Managers;
use App\Interfaces\ManagersInterface;

class CategoriesManager implements ManagersInterface
{
    private $db;

    public function __construct(\PDO $database)
    {
        $this->db = $database;
    }

    public function listAll()
    {
        return $this->db->query('SELECT id_category, link, name, date_add, date_upd FROM b_categories')->fetchAll();
    }
}