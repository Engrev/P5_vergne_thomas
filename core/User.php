<?php
namespace App\Core;

/**
 * Class User
 * @package App\Core
 */
class User
{
    private $id_user;
    private $id_group;
    private $lastname;
    private $firstname;
    private $email;
    private $online = false;

    /**
     * User constructor.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->setIdUser($user->id_user);
        $this->setIdGroup($user->id_group);
        $this->setLastname($user->lastname);
        $this->setFirstname($user->firstname);
        $this->setEmail($user->email);
        $this->setOnline(true);
    }

    /**
     * @return int
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param int $id_user
     */
    public function setIdUser($id_user)
    {
        $this->id_user = intval($id_user);
    }

    /**
     * @return int
     */
    public function getIdGroup()
    {
        return $this->id_group;
    }

    /**
     * @param int $id_group
     */
    public function setIdGroup($id_group)
    {
        $this->id_group = intval($id_group);
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = strval($lastname);
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = strval($firstname);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = strval($email);
    }

    /**
     * @return bool
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * @param bool $online
     */
    public function setOnline($online)
    {
        $this->online = boolval($online);
    }
}