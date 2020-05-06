<?php
namespace App\Core;
use App\Interfaces\HttpcodesInterface;

/**
 * Class RouterException
 * @package App\Core
 *
 * To differentiate between code exceptions and routing exceptions.
 */
class RouterException extends \Exception implements HttpcodesInterface
{
    public function display($http_code)
    {
        if (is_int($http_code) && array_key_exists($http_code, self::codes)) {
            header('HTTP/1.1 '.$http_code.' '.self::codes[$http_code]);
            echo Session::getInstance()->read('Twig')->render('errors/'.$http_code.'.twig', ['error_message'=>$this->getMessage()]);
        }
    }
}