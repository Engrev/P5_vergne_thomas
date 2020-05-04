<?php
namespace App\Core;

/**
 * Class RouterException
 * @package App\Core
 *
 * To differentiate between code exceptions and routing exceptions.
 */
class RouterException extends \Exception
{
    const http_codes = [
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '500' => 'Internal Server Error',
        '502' => 'Bad Gateway ou Proxy Error',
        '503' => 'Service Unavailable'
    ];

    public function display($http_code)
    {
        if (is_int($http_code) && in_array($http_code, self::http_codes)) {
            header('HTTP/1.0 '.$http_code.' '.self::http_codes[$http_code]);
            echo Session::getInstance()->read('Twig')->render('errors/'.$http_code.'.twig', ['error_message'=>$this->getMessage()]);
        }
    }
}