<?php
namespace App\Managers;
use App\Interfaces\ManagersInterface;

/**
 * Class CategoriesManager
 * @package App\Managers
 */
class CategoriesManager implements ManagersInterface
{
    private $db;

    /**
     * CategoriesManager constructor.
     *
     * @param \PDO $database
     */
    public function __construct(\PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Get all the categories.
     *
     * @return array
     */
    public function listAll()
    {
        return $this->db->query('SELECT id_category, link, name, date_add, date_upd FROM b_categories')->fetchAll();
    }
}