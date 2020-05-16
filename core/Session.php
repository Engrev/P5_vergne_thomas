<?php
namespace App\Core;

/**
 * Class Session
 * @package App\Core
 */
class Session
{
    private static $instance;

    /**
     * Session constructor.
     */
    public function __construct() {
        session_start();
    }

    /**
     * Get instance's Session.
     *
     * @return Session
     */
    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    /**
     * Create a session.
     *
     * @param string $key
     * @param        $value
     */
    public function write(string $key, $value)
    {
        $_SESSION['App'][$key] = $value;
    }

    /**
     * Read a session.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function read(string $key)
    {
        return isset($_SESSION['App'][$key]) ? $_SESSION['App'][$key] : null;
    }

    /**
     * Delete a session.
     *
     * @param string $key
     */
    public function delete(string $key)
    {
        unset($_SESSION['App'][$key]);
    }

    /**
     * Check if there is a flash message in session.
     *
     * @return bool
     */
    public function hasFlashes()
    {
        return isset($_SESSION['Flash']);
    }

    /**
     * Read a flash message.
     *
     * @return array
     */
    public function readFlash()
    {
        $flash = $_SESSION['Flash'];
        unset($_SESSION['Flash']);
        return $flash;
    }

    /**
     * Create a flash message.
     *
     * @param string $alert
     * @param string $message
     */
    public function writeFlash(string $alert, string $message)
    {
        $_SESSION['Flash'][$alert][] = $message;
    }

    /**
     * Registers an url in session especially when accessing a page requiring a connection to the account to be able to redirect later.
     *
     * @param string $url
     */
    public function saveLink(string $url)
    {
        $_SESSION['App']['link'] = $url;
    }

    /**
     * Check if there is an url in session.
     *
     * @return bool
     */
    public function hasLink()
    {
        return isset($_SESSION['App']['link']);
    }

    /**
     * Get an url in session and delete it.
     *
     * @return mixed
     */
    public function readLink()
    {
        $link = $_SESSION['App']['link'];
        unset($_SESSION['App']['link']);
        return $link;
    }

    /*public function __destruct()
    {
        session_destroy();
    }*/
}