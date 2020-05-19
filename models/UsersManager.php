<?php
namespace Blog\Managers;
use Blog\Interfaces\ManagersInterface;
use Blog\Core\Session;
use Blog\Core\User;

/**
 * Class UsersManager
 * @package Blog\Managers
 */
class UsersManager implements ManagersInterface
{
    const TOKEN_SHA1 = '7s0upk';
    private $db;

    /**
     * UsersManager constructor.
     *
     * @param \PDO $database
     */
    public function __construct(\PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Check if an account already exists with this email address.
     *
     * @param string $email
     *
     * @return bool
     */
    public function checkUserExist(string $email)
    {
        $user = $this->db->query('SELECT id_user FROM b_users WHERE email = ?', [$email])->fetch();
        if (!$user) {
            return true;
        }
        return false;
    }

    /**
     * Get all groups.
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->db->query('SELECT id_group, name FROM b_groups ORDER BY name')->fetchAll();
    }

    /**
     * Creates an account.
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param string $password
     * @param int $password
     */
    public function create(string $lastname, string $firstname, string $email, string $password, int $group)
    {
        $password = $this->hashPassword($password);
        $params = [
            'id_group' => $group,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'email' => $email,
            'password' => $password
        ];
        $this->db->query('INSERT INTO b_users (id_group, lastname, firstname, email, password) VALUES (:id_group, :lastname, :firstname, :email, :password)', $params);
        $id_user = $this->db->lastInsertId();
        $this->db->query('INSERT INTO b_users_infos (id_user) VALUES (:id_user)', ['id_user'=>$id_user]);
    }

    /**
     * Updates an account and a user instance.
     *
     * @param string    $lastname user mode
     * @param string    $firstname user mode
     * @param string    $email user mode
     * @param string    $password user mode
     * @param User|null $user user mode
     * @param int|null  $id_user admin mode
     * @param int|null  $id_group admin mode
     * @param int|null  $is_active admin mode
     */
    public function update(string $lastname, string $firstname, string $email, string $password = null, User $user = null, int $id_user = null, int $id_group = null, int $is_active = null)
    {
        if (!is_null($password)) {
            $password = $this->hashPassword($password);
            $this->db->query('UPDATE b_users SET password = ? WHERE id_user = ?', [$password, $user->getIdUser()]);
        }
        if (!is_null($id_user)) {
            $id_user = intval($id_user); // admin mode
        } else {
            $id_user = $user->getIdUser(); // user mode
        }
        if (!is_null($id_group)) {
            $id_group = intval($id_group); // admin mode
        } else {
            $id_group = $user->getIdGroup(); // user mode
        }
        if (!is_null($is_active)) {
            $is_active = intval($is_active); // admin mode
        } else {
            $is_active = 1; // user mode
        }
        $params = [
            'id_group' => $id_group,
            'lastname' => strval($lastname),
            'firstname' => strval($firstname),
            'email' => strval($email),
            'is_active' => $is_active,
            'id_user' => $id_user
        ];
        $this->db->query('UPDATE b_users SET id_group = :id_group, lastname = :lastname, firstname = :firstname, email = :email, is_active = :is_active, date_upd = NOW() WHERE id_user = :id_user', $params);
        if (!is_null($user)) {
            $user->setLastname($lastname);
            $user->setFirstname($firstname);
            $user->setEmail($email);
        }
    }

    /**
     * Updates user information.
     *
     * @param array $infos
     * @param int   $id_user
     */
    public function updateInfos(array $infos, int $id_user)
    {
        $params = [
            'website' => strval($infos['website']),
            'linkedin' => strval($infos['linkedin']),
            'github' => strval($infos['github']),
            'id_user' => $id_user
        ];
        $this->db->query('UPDATE b_users_infos SET website = :website, linkedin = :linkedin, github = :github WHERE id_user = :id_user', $params);
        $this->db->query('UPDATE b_users SET date_upd = NOW() WHERE id_user = ?', [$id_user]);
    }

    /**
     * Delete an account.
     *
     * @param int    $id_user
     * @param string|null $email
     */
    public function delete(int $id_user, string $email = null)
    {
        if (!is_null($email)) {
            $this->disconnect();
        }
        $this->db->query('DELETE FROM b_users_infos WHERE id_user = ?', [$id_user]);
        $this->db->query('DELETE FROM b_users WHERE id_user = ?', [$id_user]);
        if (!is_null($email)) {
            $method = __FUNCTION__.'Account';
            Session::getInstance()->read('Mail')->$method($email);
        }
    }

    /**
     * Enables or disables a user.
     *
     * @param int $state
     * @param int $id_user
     */
    public function activate(int $state, int $id_user)
    {
        switch ($state) {
            case 0:
                $this->db->query('UPDATE b_users SET is_active = 1, date_upd = NOW() WHERE id_user = ?', [$id_user]);
                break;
            case 1:
                $this->db->query('UPDATE b_users SET is_active = 0, date_upd = NOW() WHERE id_user = ?', [$id_user]);
                break;
        }
    }

    /**
     * Get user information (the social part).
     *
     * @param int $id_user
     *
     * @return mixed
     */
    public function getInfos(int $id_user)
    {
        return $this->db->query('SELECT website, linkedin, github FROM b_users_infos WHERE id_user = ?', [$id_user])->fetch();
    }

    /**
     * Get all users.
     *
     * @return array
     */
    public function listAll()
    {
        return $this->db->query("SELECT U.id_user, U.lastname, U.firstname, U.email, DATE_FORMAT(U.last_connection, '%d/%m/%Y %H:%i:%s') AS last_connection, IF(U.is_active = 1, true, false) AS active, G.name AS group_name
                                          FROM b_users AS U
                                          INNER JOIN b_groups AS G
                                            ON U.id_group = G.id_group
                                          ORDER BY U.lastname")->fetchAll();
    }

    /**
     * Get a user.
     *
     * @param int $id_user
     *
     * @return mixed
     */
    public function list(int $id_user)
    {
        return $this->db->query('SELECT id_user, id_group, lastname, firstname, email, is_active FROM b_users WHERE id_user = ?', [$id_user])->fetch();
    }

    /**
     * Connect a user.
     *
     * @param string $email
     * @param string $password
     * @param bool   $remember_me
     *
     * @return User|string
     */
    public function connect(string $email, string $password, bool $remember_me)
    {
        $user = $this->db->query('SELECT U.id_user, U.id_group, U.lastname, U.firstname, U.email, U.password, U.is_active, F.path AS avatar
                                           FROM b_users AS U
                                           LEFT JOIN b_files AS F
                                             ON F.id_file = U.avatar
                                           WHERE U.email = ?', [$email])->fetch();
        if (!empty($user)) {
            $is_active = $user->is_active == 1 ? true : false;
            if ($is_active) {
                if (password_verify($password, $user->password)) {
                    if ($remember_me) {
                        $this->remember($user->id_user);
                    }
                    $this->db->query('UPDATE b_users SET last_connection = NOW() WHERE id_user = ?', [$user->id_user]);
                    return new User($user);
                }
                return 'Mot de passe incorrect.';
            }
            return 'Ce compte est désactivé.';
        }
        return 'Aucun compte n\'est lié à cette adresse email.';
    }

    /**
     * Connect a user with the cookie.
     *
     * @return User|bool
     */
    public function connectFromCookie()
    {
        if (isset($_COOKIE['remember'])) {
            $remember_token = $_COOKIE['remember'];
            $parts = explode('#', $remember_token);
            $id_user = intval($parts[0]);
            $user = $this->db->query('SELECT U.id_user, U.id_group, U.lastname, U.firstname, U.email, U.password, U.is_active, U.remember_token, F.path AS avatar
                                               FROM b_users AS U
                                               LEFT JOIN b_files AS F
                                                 ON F.id_file = U.avatar
                                               WHERE U.id_user = ?', [$id_user])->fetch();
            if ($user) {
                $is_active = $user->is_active == 1 ? true : false;
                if ($is_active) {
                    $expected = $id_user . '#' . $user->remember_token . sha1($id_user . self::TOKEN_SHA1);
                    if ($expected == $remember_token) {
                        $this->db->query('UPDATE b_users SET last_connection = NOW() WHERE id_user = ?', [$id_user]);
                        $this->remember($id_user);
                        return new User($user);
                    }
                }
            }
            setcookie('remember', null, -1);
        }
        return false;
    }

    /**
     * Disconnect a user.
     */
    public function disconnect()
    {
        Session::getInstance()->delete('User');
        setcookie('remember', null, -1);
    }

    /**
     * Creates a token for resetting a password.
     *
     * @param string $email
     *
     * @return bool
     */
    public function resetToken(string $email)
    {
        $user = $this->db->query('SELECT id_user FROM b_users WHERE email = ?', [$email])->fetch();
        if ($user) {
            $reset_token = $this->token(60);
            $this->db->query('UPDATE b_users SET reset_token = ?, date_reset_token = NOW() WHERE id_user = ?', [$reset_token, $user->id_user]);
            $method = __FUNCTION__;
            Session::getInstance()->read('Mail')->$method($email, $user->id_user, $reset_token);
            return true;
        }
        return false;
    }

    /**
     * Checks the validity of a password token.
     *
     * @param int    $id_user
     * @param string $token
     *
     * @return mixed
     */
    public function checkResetPassword(int $id_user, string $token)
    {
        return $this->db->query('SELECT email
                                          FROM b_users
                                          WHERE id_user = ? AND reset_token IS NOT NULL AND reset_token = ? AND date_reset_token > DATE_SUB(NOW(), INTERVAL 30 MINUTE)', [$id_user, $token])->fetch();
    }

    /**
     * Hash a password.
     *
     * @param string $password
     *
     * @return false|string|null
     */
    public function hashPassword(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Save the new password.
     *
     * @param string $new_password
     * @param int    $id_user
     * @param bool   $reset
     */
    public function newPassword(string $new_password, int $id_user, $reset = false)
    {
        $password = $this->hashPassword($new_password);
        if ($reset) {
            $this->db->query('UPDATE b_users SET password = ?, reset_token = NULL, date_reset_token = NULL, date_upd = NOW() WHERE id_user = ?', [$password, $id_user]);
        } else {
            $this->db->query('UPDATE b_users SET password = ?, date_upd = NOW() WHERE id_user = ?', [$password, $id_user]);
        }
    }

    /**
     * Create a token.
     *
     * @param int $length
     *
     * @return false|string
     */
    private function token(int $length)
    {
        $alphabet = '0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN';
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }

    /**
     * Creates a cookie for the connection.
     *
     * @param int $id_user
     */
    public function remember(int $id_user)
    {
        $remember_token = $this->token(250);
        $this->db->query('UPDATE b_users SET remember_token = ? WHERE id_user = ?', [$remember_token, $id_user]);
        setcookie('remember', $id_user . '#' . $remember_token . sha1($id_user . self::TOKEN_SHA1), time() + (30*24*3600), _COOKIE_PATH_, _COOKIE_DOMAIN_, false, true);
    }

    //Saves uploaded files to the database.
    public function saveUpload(array $file, int $id)
    {
        $params = [
            'path' => $file['path'],
            'name' => $file['name'],
            'uploaded_name' => $file['uploaded_name']
        ];
        $this->db->query('INSERT INTO b_files (path, name, uploaded_name, date_add, date_upd) VALUES (:path, :name, :uploaded_name, NOW(), NOW())', $params);
        $id_file = $this->db->lastInsertId();
        $this->db->query('UPDATE b_users SET avatar = ? WHERE id_user = ?', [$id_file, $id]);
    }
}