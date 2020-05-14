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

    /**
     * Saves uploaded files to the database.
     *
     * @param array $file
     * @param int   $id
     *
     * @return mixed
     */
    public function saveUpload(array $file, int $id);
}