<?php
namespace App\Managers;
use App\Interfaces\ManagersInterface;
use App\Exceptions\ManagerException;

/**
 * Class PostsManager
 * @package App\Managers
 */
class PostsManager implements ManagersInterface
{
    private $db;

    /**
     * PostsManager constructor.
     *
     * @param \PDO $database
     */
    public function __construct(\PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Get the latest posts.
     *
     * @param mixed $param
     *
     * @return array
     */
    public function listLasts($param)
    {
        switch ($param) {
            default:
                $where = 'P.is_valid = 1 ORDER BY P.id_post DESC';
                break;
            case is_int($param):
                $where = 'P.is_valid = 1 ORDER BY P.id_post DESC LIMIT '.intval($param);
                break;
        }
        return $this->db->query("SELECT P.id_post, P.id_category, P.link AS p_link, P.title, P.description, P.content, P.author, P.is_valid, P.date_add, P.date_upd, 
                                                 DATE_FORMAT(P.date_add, '%d %M %Y') AS date_add_fr, DATE_FORMAT(P.date_upd, '%d %M %Y') AS date_upd_fr, 
                                                 C.name, C.link AS c_link, 
                                                 U.lastname, U.firstname
                                          FROM b_posts AS P
                                          LEFT JOIN b_categories AS C
                                            ON C.id_category = P.id_category
                                          INNER JOIN b_users AS U
                                            ON U.id_user = P.author
                                          WHERE {$where}")->fetchAll();
    }

    /**
     * Counts the number of posts by category.
     *
     * @return array
     */
    public function countPostsCategory()
    {
        $results = $this->db->query('SELECT id_category, COUNT(id_post) FROM b_posts GROUP BY id_category')->fetchAll(\PDO::FETCH_COLUMN|\PDO::FETCH_GROUP);
        foreach ($results as $key => $value) {
            $results[$key] = $value[0];
        }
        return $results;
    }

    /**
     * Get a post.
     *
     * @param string $id
     * @param string $slug
     *
     * @return mixed
     * @throws ManagerException
     */
    public function list(string $id, string $slug)
    {
        $post = $this->db->query("SELECT P.id_post, P.id_category, P.link AS p_link, P.title, P.description, P.content, P.author, P.is_valid, P.date_add, P.date_upd, 
                                                  DATE_FORMAT(P.date_add, '%d %M %Y') AS date_add_fr, DATE_FORMAT(P.date_upd, '%d %M %Y') AS date_upd_fr, 
                                                  C.name, C.link AS c_link, 
                                                  U.lastname, U.firstname
                                           FROM b_posts AS P
                                           INNER JOIN b_categories AS C
                                             ON C.id_category = P.id_category
                                           INNER JOIN b_users AS U
                                             ON U.id_user = P.author
                                           WHERE P.id_post = ?", [$id])->fetch();
        if ($post->p_link) {
            if ($post->p_link === $id.'-'.$slug) {
                if ($post->is_valid == 1) {
                    return $post;
                }
            }
        }
        throw new ManagerException('Cet article n\'existe pas');
    }

    /**
     * Get posts from a category with pagination.
     *
     * @param int        $id_category
     * @param array|null $pagination
     *
     * @return array
     */
    public function listPostsCategory(int $id_category, array $pagination = null)
    {
        $limit = 10;
        if (is_array($pagination)) {
            $limit = "{$pagination['limit']} OFFSET {$pagination['offset']}";
        }
        return $this->db->query("SELECT SQL_CALC_FOUND_ROWS P.id_post, P.id_category, P.link AS p_link, P.title, P.description, P.content, P.author, P.is_valid, P.date_add, P.date_upd, 
                                                 DATE_FORMAT(P.date_add, '%d %M %Y') AS date_add_fr, DATE_FORMAT(P.date_upd, '%d %M %Y') AS date_upd_fr, 
                                                 C.name, C.link AS c_link, 
                                                 U.lastname, U.firstname
                                          FROM b_posts AS P
                                          INNER JOIN b_categories AS C
                                            ON C.id_category = P.id_category
                                          INNER JOIN b_users AS U
                                            ON U.id_user = P.author
                                          WHERE P.id_category = ?
                                          ORDER BY P.id_post DESC
                                          LIMIT {$limit}", [$id_category])->fetchAll();
    }

    /**
     * Get all of a user's posts.
     *
     * @param int $id_user
     *
     * @return array
     */
    public function listAll(int $id_user)
    {
        return $this->db->query("SELECT P.id_post, P.link AS p_link, P.title, P.is_valid, DATE_FORMAT(P.date_add, '%d/%m/%Y %H:%i') AS date_add, DATE_FORMAT(P.date_upd, '%d/%m/%Y %H:%i') AS date_upd, C.name, C.link AS c_link
                                          FROM b_posts AS P
                                          INNER JOIN b_categories AS C
                                            ON C.id_category = P.id_category
                                          WHERE P.author = ?
                                          ORDER BY P.date_add DESC", [$id_user])->fetchAll();
    }

    /**
     * Change the author of a post when its author has deleted his account.
     *
     * @param int $id_author
     */
    public function deletedAuthor(int $id_author)
    {
        $this->db->query('UPDATE b_posts SET author = 0 WHERE author = ?', [$id_author]);
    }

    //Saves uploaded files to the database.
    public function saveUpload(array $file, int $id)
    {
        // TODO: Implement saveUpload() method.
    }
}