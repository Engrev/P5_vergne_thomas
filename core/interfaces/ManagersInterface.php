<?php
namespace App\Interfaces;

/**
 * Interface ManagersInterface
 * @package App\Interfaces
 */
interface ManagersInterface
{
    /**
     * ManagersInterface constructor.
     *
     * @param \PDO $database
     */
    public function __construct(\PDO $database);
}