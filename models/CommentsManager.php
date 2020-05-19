<?php
namespace Blog\Managers;
use Blog\Interfaces\ManagersInterface;

/**
 * Class CommentsManager
 * @package Blog\Managers
 */
class CommentsManager implements ManagersInterface
{
    private $db;

    /**
     * CommentsManager constructor.
     *
     * @param \PDO $database
     */
    public function __construct(\PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Create a comment.
     *
     * @param string      $comment
     * @param int         $id_post
     * @param string|null $name
     * @param string|null $email
     * @param int|null    $id_user
     */
    public function create(string $comment, int $id_post, string $name = null, string $email = null, int $id_user = null)
    {
        $params = [
            'id_post' => intval($id_post),
            'id_user' => $id_user,
            'name' => htmlspecialchars($name),
            'email' => htmlspecialchars($email),
            'content' => nl2br(htmlspecialchars($comment))
        ];
        $this->db->query('INSERT INTO b_comments (id_post, id_user, name, email, content, date_add, date_upd) VALUES (:id_post, :id_user, :name, :email, :content, NOW(), NOW())', $params);
    }

    /**
     * Delete a comment.
     *
     * @param int $id_comment
     */
    public function delete(int $id_comment)
    {
        $this->db->query('DELETE FROM b_comments WHERE id_comment = ?', [intval($id_comment)]);
    }

    /**
     * Validate a comment.
     *
     * @param int $id_comment
     */
    public function validate(int $id_comment)
    {
        $this->db->query('UPDATE b_comments SET is_valid = 1, date_upd = NOW() WHERE id_comment = ?', [intval($id_comment)]);
    }

    /**
     * Get all comments of a post.
     *
     * @param int $id_post
     *
     * @return array
     */
    public function display(int $id_post)
    {
        return $this->db->query('SELECT C.id_comment, C.id_user, C.name, C.content, C.date_add, U.lastname, U.firstname
                                          FROM b_comments AS C
                                          LEFT JOIN b_users AS U
                                            ON U.id_user = C.id_user
                                          WHERE C.is_valid = 1 AND C.id_post = ?
                                          ORDER BY C.date_add DESC', [intval($id_post)])->fetchAll();
    }

    /**
     * Get all comments of a post.
     *
     * @return array
     */
    public function listAll()
    {
        return $this->db->query("SELECT C.id_comment, C.id_user, C.id_post, C.name, C.email, C.content, DATE_FORMAT(C.date_add, '%d/%m/%Y %H:%i:%s') AS date_add, 
                                                 P.title AS post_title, P.link AS post_link, 
                                                 U.lastname, U.firstname
                                          FROM b_comments AS C
                                          INNER JOIN b_posts AS P
                                            ON P.id_post = C.id_post
                                          LEFT JOIN b_users AS U
                                            ON U.id_user = C.id_user
                                          WHERE C.is_valid = 0
                                          ORDER BY C.date_add")->fetchAll();
    }

    /**
     * Rewrite the date of a comment.
     *
     * @param string $date
     *
     * @return string
     * @throws \Exception
     */
    public function dateIntervalComments(string $date)
    {
        $today = new \DateTime('now');
        $date = new \DateTime($date);
        $interval = $date->diff($today);
        $years = $interval->format('%y');
        $months = $interval->format('%m');
        $days = $interval->format('%d');
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        $seconds = $interval->format('%s');

        if ($years > 0) {
            if ($years == 1) {
                $date_comment = 'Il y a environ '.$years.' an';
            } else {
                $date_comment = 'Il y a environ '.$years.' ans';
            }
        } elseif ($months > 0) {
            $date_comment = 'Il y a '.$months.' mois';
        } elseif ($days > 0) {
            if ($days >= 21 && $days < 28) {
                $date_comment = 'Il y a 3 semaines';
            } elseif ($days >= 14 && $days < 21) {
                $date_comment = 'Il y a 2 semaines';
            } elseif ($days == 7 || $days > 7 && $days < 14) {
                $date_comment = 'Il y a 1 semaine';
            } elseif ($days == 1) {
                $date_comment = 'Hier';
            } else {
                $date_comment = 'Il y a '.$days.' jours';
            }
        } elseif ($hours > 0) {
            if ($hours == 1) {
                $date_comment = 'Il y a '.$hours.' heure';
            } else {
                $date_comment = 'Il y a '.$hours.' heures';
            }
        } elseif ($minutes > 0) {
            if ($minutes == 1) {
                $date_comment = 'Il y a '.$minutes.' minute';
            } else {
                $date_comment = 'Il y a '.$minutes.' minutes';
            }
        } elseif ($seconds > 0) {
            $date_comment = 'Il y a quelques secondes';
        } else {
            $date_comment = '';
        }

        return $date_comment;
    }

    //Saves uploaded files to the database.
    public function saveUpload(array $file, int $id)
    {
        // TODO: Implement saveUpload() method.
    }
}