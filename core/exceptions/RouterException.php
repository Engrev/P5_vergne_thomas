<?php
namespace Blog\Exceptions;
use Blog\Core\Session;
use Blog\Interfaces\HttpcodesInterface;

/**
 * Class RouterException
 * @package Blog\Exceptions
 *
 * To differentiate between code exceptions and routing exceptions.
 */
class RouterException extends \Exception implements HttpcodesInterface
{
    /**
     * Displays error pages.
     *
     * @param int  $http_code
     * @param bool $use_message
     *
     * @return mixed|void
     */
    public function display($http_code, $use_message = false)
    {
        if (is_int($http_code) && array_key_exists($http_code, self::codes)) {
            header('HTTP/1.1 '.$http_code.' '.self::codes[$http_code]);
            echo Session::getInstance()->read('Twig')->render('errors/'.$http_code.'.twig', ['error_message'=>['use'=>$use_message, 'message'=>$this->getMessage()]]);
            exit();
        }
    }
}