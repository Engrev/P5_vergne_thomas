<?php
namespace Blog\Managers;
use Blog\Interfaces\ManagersInterface;
use Blog\Exceptions\ManagerException;

/**
 * Class PostsManager
 * @package Blog\Managers
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
     * Create a post.
     *
     * @param array $posts
     * @param int   $id_user
     */
    public function create(array $posts, int $id_user)
    {
        $title = htmlspecialchars($posts['title']);
        $description = htmlspecialchars($posts['description']);
        $id_category = intval($posts['categorie']);
        $publish = intval($posts['publish']);
        $link = $this->transformToUrl($posts['title']);
        $params = [
            'id_category' => $id_category,
            'link' => '#',
            'title' => $title,
            'description' => $description,
            'content' => $posts['content'],
            'author' => $id_user,
            'published' => $publish
        ];
        $this->db->query('INSERT INTO b_posts (id_category, link, title, description, content, author, published, date_add, date_upd)
                                   VALUES (:id_category, :link, :title, :description, :content, :author, :published, NOW(), NOW())', $params);
        $id_post = $this->db->lastInsertId();
        $link = strval($id_post).'-'.$link;
        $this->db->query('UPDATE b_posts SET link = ? WHERE id_post = ?', [$link, $id_post]);
    }

    /**
     * Edit a post.
     *
     * @param array $posts
     * @param int   $id_post
     */
    public function update(array $posts, int $id_post)
    {
        $title = htmlspecialchars($posts['title']);
        $description = htmlspecialchars($posts['description']);
        $id_category = intval($posts['categorie']);
        $publish = intval($posts['publish']);
        $link = $this->transformToUrl($posts['title']);
        $link = strval($id_post).'-'.$link;
        $params = [
            'id_category' => $id_category,
            'link' => $link,
            'title' => $title,
            'description' => $description,
            'content' => $posts['content'],
            'published' => $publish,
            'id_post' => intval($id_post)
        ];
        $this->db->query('UPDATE b_posts
                                   SET id_category = :id_category, link = :link, title = :title, description = :description, content = :content, published = :published, date_upd = NOW()
                                   WHERE id_post = :id_post', $params);
    }

    /**
     * Delete a post.
     *
     * @param int $id_post
     */
    public function delete(int $id_post)
    {
        $this->db->query('DELETE FROM b_posts WHERE id_post = ?', [intval($id_post)]);
    }

    /**
     * Create the post link from the title.
     *
     * @param string $title
     *
     * @return string
     */
    private function transformToUrl(string $title)
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
        $str = preg_replace('~[^\\pL\d]+~u', '-', $title);
        $str = trim($str, '-');
        $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);
        $str = strtolower($str);
        $str = preg_replace('~[^-\w]+~', '', $str);
        return $str.'.html';
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
                $where = 'P.published = 1 ORDER BY P.id_post DESC';
                break;
            case is_int($param):
                $where = 'P.published = 1 ORDER BY P.id_post DESC LIMIT '.intval($param);
                break;
        }
        return $this->db->query("SELECT P.id_post, P.id_category, P.link AS p_link, P.title, P.description, P.content, P.author, P.published, P.date_add, P.date_upd, 
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
     * Display a post.
     *
     * @param string   $id
     * @param string   $slug
     * @param int|null $id_user
     *
     * @return mixed
     * @throws ManagerException
     */
    public function display(string $id, string $slug, int $id_user = null)
    {
        $post = $this->db->query("SELECT P.id_post, P.id_category, P.link AS p_link, P.title, P.description, P.content, P.author, P.published, P.date_add, P.date_upd, 
                                                  DATE_FORMAT(P.date_add, '%d %M %Y') AS date_add_fr, DATE_FORMAT(P.date_upd, '%d %M %Y') AS date_upd_fr, 
                                                  C.name, C.link AS c_link, 
                                                  U.lastname, U.firstname
                                           FROM b_posts AS P
                                           INNER JOIN b_categories AS C
                                             ON C.id_category = P.id_category
                                           INNER JOIN b_users AS U
                                             ON U.id_user = P.author
                                           WHERE P.id_post = ?", [intval($id)])->fetch();
        if ($post->p_link) {
            if ($post->p_link === $id.'-'.$slug) {
                if ($post->published == 1 || !is_null($id_user) && $post->published == 0 && $post->author == $id_user) {
                    return $post;
                }
            }
        }
        throw new ManagerException('Cet article n\'existe pas');
    }

    /**
     * Enables or disables a post.
     *
     * @param int $state
     * @param int $id_post
     */
    public function activate(int $state, int $id_post)
    {
        switch ($state) {
            case 0:
                $this->db->query('UPDATE b_posts SET published = 1, date_upd = NOW() WHERE id_post = ?', [intval($id_post)]);
                break;
            case 1:
                $this->db->query('UPDATE b_posts SET published = 0, date_upd = NOW() WHERE id_post = ?', [intval($id_post)]);
                break;
        }
    }

    /**
     * Get a post.
     *
     * @param string $id
     *
     * @return mixed
     * @throws ManagerException
     */
    public function list(int $id)
    {
        $post = $this->db->query("SELECT id_post, id_category, link, title, description, content, author, published
                                           FROM b_posts
                                           WHERE id_post = ?", [intval($id)])->fetch();
        if (!empty($post)) {
            return $post;
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
        return $this->db->query("SELECT SQL_CALC_FOUND_ROWS P.id_post, P.id_category, P.link AS p_link, P.title, P.description, P.content, P.author, P.published, P.date_add, P.date_upd, 
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
                                          LIMIT {$limit}", [intval($id_category)])->fetchAll();
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
        return $this->db->query("SELECT P.id_post, P.link AS p_link, P.title, IF(P.published = 1, true, false) AS published, DATE_FORMAT(P.date_add, '%d/%m/%Y %H:%i') AS date_add, DATE_FORMAT(P.date_upd, '%d/%m/%Y %H:%i') AS date_upd, C.name, C.link AS c_link
                                          FROM b_posts AS P
                                          INNER JOIN b_categories AS C
                                            ON C.id_category = P.id_category
                                          WHERE P.author = ?
                                          ORDER BY P.date_add DESC", [intval($id_user)])->fetchAll();
    }

    /**
     * Change the author of a post when its author has deleted his account.
     *
     * @param int $id_author
     */
    public function deletedAuthor(int $id_author)
    {
        $this->db->query('UPDATE b_posts SET author = 0 WHERE author = ?', [intval($id_author)]);
    }

    /**
     * Change the category of a post.
     *
     * @param int $id_category
     */
    public function deletedCategory(int $id_category)
    {
        $this->db->query('UPDATE b_posts SET id_category = 1 WHERE id_category = ?', [intval($id_category)]);
    }

    //Saves uploaded files to the database.
    public function saveUpload(array $file, int $id)
    {
        // TODO: Implement saveUpload() method.
    }
}