<?php
namespace App\Core;

class Session
{
    static $instance;
    
    public function __construct() {
        session_start();
    }

    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    public function write($key, $value)
    {
        $_SESSION['App'][$key] = $value;
    }
    
    public function read($key)
    {
        return isset($_SESSION['App'][$key]) ? $_SESSION['App'][$key] : null;
    }
    
    public function delete($key)
    {
        unset($_SESSION['App'][$key]);
    }

    public function saveLink($url)
    {
        $_SESSION['App']['link'] = $url;
    }

    public function hasLink()
    {
        return isset($_SESSION['App']['link']);
    }

    public function getLink()
    {
        $link = $_SESSION['App']['link'];
        unset($_SESSION['App']['link']);
        return $link;
    }
}