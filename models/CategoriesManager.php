<?php
namespace App\Managers;
use App\Exceptions\ManagerException;
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
        return $this->db->query('SELECT id_category, link, name, date_add, date_upd FROM b_categories ORDER BY name')->fetchAll();
    }

    /**
     * Get a category.
     *
     * @param string $id
     * @param string $slug
     *
     * @return mixed
     * @throws ManagerException
     */
    public function list(string $id, string $slug)
    {
        $category = $this->db->query('SELECT id_category, link, name, date_add, date_upd FROM b_categories WHERE id_category = ?', [$id])->fetch();
        if ($category->link) {
            if ($category->link === $id.'-'.$slug) {
                return $category;
            }
        }
        throw new ManagerException('Cette catégorie n\'existe pas');
    }

    //Saves uploaded files to the database.
    public function saveUpload(array $file, int $id)
    {
        // TODO: Implement saveUpload() method.
    }
}