<?php
namespace App\Exceptions;
use App\Core\Session;
use App\Interfaces\HttpcodesInterface;
use PHPMailer\PHPMailer\Exception;

/**
 * Class MailException
 * @package App\Exceptions
 */
class MailException extends Exception implements HttpcodesInterface
{
    /**
     * Displays error pages.
     *
     * @param int  $http_code
     * @param bool $use_message
     *
     * @return mixed|void
     */
    public function display($http_code, $use_message = false, $error_info = '')
    {
        if (is_int($http_code) && array_key_exists($http_code, self::codes)) {
            header('HTTP/1.1 '.$http_code.' '.self::codes[$http_code]);
            echo Session::getInstance()->read('Twig')->render('errors/'.$http_code.'.twig', ['error_message'=>['use'=>$use_message, 'message'=>$error_info]]);
            exit();
        }
    }
}