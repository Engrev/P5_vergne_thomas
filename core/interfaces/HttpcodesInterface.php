<?php
namespace App\Interfaces;

interface HttpcodesInterface
{
    const codes = [
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '500' => 'Internal Server Error',
        '502' => 'Bad Gateway ou Proxy Error',
        '503' => 'Service Unavailable'
    ];

    public function display($http_code);
}