<?php
namespace App\Core;

/**
 * Class Validator
 * @package App\Core
 */
class Validator
{
    private $data;
    private $errors = [];

    /**
     * Validator constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the value of a $_POST.
     *
     * @param $field
     *
     * @return mixed|null
     */
    private function getField($field) {
        if (!isset($this->data[$field])) {
            return null;
        }
        return $this->data[$field];
    }

    /**
     * Check if a $_POST is not empty and that the passwords match.
     *
     * @param        $field
     * @param string $errorMsg
     */
    public function isConfirmed($field, $errorMsg = '')
    {
        $value = $this->getField($field);
        if (empty($value) || $value != $this->getField($field . '_confirm')) {
            $this->errors[$field] = $errorMsg;
        }
    }

    /**
     * Check if the password fields is a good match.
     *
     * @param        $field
     * @param string $errorMsg
     */
    public function isPassword($field, $errorMsg = '')
    {
        if (!preg_match('#^[a-zA-Z0-9_-]+$#', $this->getField($field)) && !preg_match('#^[a-zA-Z0-9_-]+$#', $this->getField($field . '_confirm'))) {
            $this->errors[$field] = $errorMsg;
        }
    }

    /**
     * Check if an email field has the correct form.
     *
     * @param $field
     * @param string $errorMsg
     */
    public function isEmail($field, $errorMsg = '')
    {
        if (!filter_var($this->getField($field), FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $errorMsg;
        }
    }

    /**
     * Checks if there is no error in validator.
     *
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * Get validator errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}