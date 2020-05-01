<?php
namespace App\Managers;
use App\Interfaces\ManagersInterface;

class PostsManager implements ManagersInterface
{
    private $db;

    public function __construct(\PDO $database)
    {
        $this->db = $database;
    }

    public function listLasts($param)
    {
        switch ($param) {
            default:
                $where = 'is_valid = 1 ORDER BY DESC';
                break;
            case is_int($param):
                $where = 'is_valid = 0 ORDER BY DESC LIMIT'.$param;
                break;
        }
        return $this->db->query('SELECT P.id_post, P.id_category, P.link AS p_link, P.title, P.description, P.content, P.author, P.is_valid, P.date_add, P.date_upd, C.name, C.link AS c_link
                                          FROM b_posts AS P
                                          LEFT JOIN b_categories AS C
                                            ON C.id_category = P.id_category
                                          WHERE :where', ['where'=>$where])->fetchAll();
    }

    public function countPostsCategory()
    {
        $results = $this->db->query('SELECT id_category, COUNT(id_post) FROM b_posts GROUP BY id_category')->fetchAll(\PDO::FETCH_COLUMN|\PDO::FETCH_GROUP);
        foreach ($results as $key => $value) {
            $results[$key] = $value[0];
        }
        return $results;
    }
}