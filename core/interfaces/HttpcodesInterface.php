<?php
namespace App\Interfaces;

/**
 * Interface HttpcodesInterface
 * @package App\Interfaces
 */
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

    /**
     * Displays error pages.
     *
     * @param int $http_code
     * @param bool $use_message
     *
     * @return mixed
     */
    public function display(int $http_code, $use_message = false);
}