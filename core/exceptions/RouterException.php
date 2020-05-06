<?php
namespace App\Exceptions;
use App\Core\Session;
use App\Interfaces\HttpcodesInterface;

/**
 * Class RouterException
 * @package App\Exceptions
 *
 * To differentiate between code exceptions and routing exceptions.
 */
class RouterException extends \Exception implements HttpcodesInterface
{
    /**
     * Displays error pages.
     *
     * @param int $http_code
     *
     * @return mixed|void
     */
    public function display($http_code)
    {
        if (is_int($http_code) && array_key_exists($http_code, self::codes)) {
            header('HTTP/1.1 '.$http_code.' '.self::codes[$http_code]);
            echo Session::getInstance()->read('Twig')->render('errors/'.$http_code.'.twig', ['error_message'=>$this->getMessage()]);
        }
    }
}