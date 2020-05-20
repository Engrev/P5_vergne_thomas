<?php
namespace Blog\Managers;
use Blog\Exceptions\ManagerException;
use Blog\Interfaces\ManagersInterface;

/**
 * Class CategoriesManager
 * @package Blog\Managers
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
     * Create a category.
     *
     * @param string $name
     */
    public function create(string $name)
    {
        $name_e = htmlspecialchars($name);
        $link = $this->transformToUrl($name);
        $params = [
            'name' => $name_e,
            'link' => '#'
        ];
        $this->db->query('INSERT INTO b_categories (name, link, date_add, date_upd) VALUES (:name, :link, NOW(), NOW())', $params);
        $id_category = $this->db->lastInsertId();
        $link = strval($id_category).'-'.$link;
        $this->db->query('UPDATE b_categories SET link = ? WHERE id_category = ?', [$link, $id_category]);
    }

    /**
     * Edit a category.
     *
     * @param string $name
     * @param int    $id_category
     */
    public function update(string $name, int $id_category)
    {
        $name_e = htmlspecialchars($name);
        $id_category = intval($id_category);
        $link = $this->transformToUrl($name);
        $link = strval($id_category).'-'.$link;
        $params = [
            'name' => $name_e,
            'link' => $link,
            'id_category' => intval($id_category)
        ];
        $this->db->query('UPDATE b_categories SET name = :name, link = :link, date_upd = NOW() WHERE id_category = :id_category', $params);
    }

    /**
     * Delete a category.
     *
     * @param int $id_category
     */
    public function delete(int $id_category)
    {
        $this->db->query('DELETE FROM b_categories WHERE id_category = ?', [intval($id_category)]);
    }

    /**
     * Create the category link from the name.
     *
     * @param string $name
     *
     * @return string
     */
    private function transformToUrl(string $name)
    {
        /*
         * https://www.webmaster-hub.com/publications/transformer-un-texte-en-url/
        $string = utf8_encode($title);
        $reg = '#&(.)(acute|grave|circ|uml|cedil|ring|tilde|slash);#';
        $without_accents = preg_replace($reg, '1', htmlentities($string, ENT_COMPAT, 'UTF-8'));

        $string = str_replace('ß', 'ss', $without_accents);
        $reg = '#&([a-zA-Z]{2})lig;#';
        $without_ligature = preg_replace($reg, '1', $string);

        $reg = '#(&[a-zA-Z0-9]*;)#U';
        $without_special_characters = preg_replace($reg, '-', $without_ligature);

        $to_lower = strtolower($without_special_characters);

        $reg = '#([^a-z0-9]+)#';
        $remaining = preg_replace($reg, '-', $to_lower);

        $without_dashes = trim($remaining, '-');
        return $without_dashes.'.html';*/
        /*
         * https://www.webmaster-hub.com/topic/43505-transformer-un-texte-en-url/page/2/?tab=comments#comment-337105
         */
        $str = preg_replace('~[^\\pL\d]+~u', '-', $name);
        $str = trim($str, '-');
        $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);
        $str = strtolower($str);
        $str = preg_replace('~[^-\w]+~', '', $str);
        return $str;
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
     * Display a category.
     *
     * @param string $id
     * @param string $slug
     *
     * @return mixed
     * @throws ManagerException
     */
    public function display(string $id, string $slug)
    {
        $category = $this->db->query('SELECT id_category, link, name, date_add, date_upd FROM b_categories WHERE id_category = ?', [intval($id)])->fetch();
        if ($category->link) {
            if ($category->link === $id.'-'.$slug) {
                return $category;
            }
        }
        throw new ManagerException('Cette catégorie n\'existe pas');
    }

    /**
     * Get a category.
     *
     * @param string $id
     *
     * @return mixed
     * @throws ManagerException
     */
    public function list(int $id)
    {
        $category = $this->db->query("SELECT id_category, link, name
                                               FROM b_categories
                                               WHERE id_category = ?", [intval($id)])->fetch();
        if (!empty($category)) {
            return $category;
        }
        throw new ManagerException('Cette catégorie n\'existe pas');
    }

    //Saves uploaded files to the database.
    public function saveUpload(array $file, int $id)
    {
        // TODO: Implement saveUpload() method.
    }
}