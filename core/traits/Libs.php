<?php
namespace Blog\Traits;

/**
 * Trait Libs
 * @package Blog\Traits
 */
trait Libs
{
    /**
     * Get ip_address.
     *
     * @return mixed|string
     */
    protected function getRemoteAddr()
    {
        // This condition is necessary when using CDN, don't remove it.
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR'])
            || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR']))
            || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))
        ) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                return $ips[0];
            }
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get value of a $_POST.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getPostSuperglobal(string $key) {
        return $_POST[strval($key)];
    }

    /**
     * Set a value on a $_POST.
     *
     * @param string $key
     * @param        $value
     */
    protected function setPostSuperglobal(string $key, $value) {
        $_POST[strval($key)] = $value;
    }

    /**
     * Check if a $_POST is set.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function issetPostSperglobal(string $key) {
        return isset($_POST[strval($key)]);
    }

    /**
     * Get values of an array $_POST.
     *
     * @param string $array_key
     * @param string $key
     *
     * @return mixed
     */
    protected function getArrayPostSuperglobal(string $array_key, string $key) {
        return $_POST[strval($array_key)][strval($key)];
    }

    /**
     * Set a value on an array $_POST.
     *
     * @param string $array_key
     * @param string $key
     * @param        $value
     */
    protected function setArrayPostSuperglobal(string $array_key, string $key, $value) {
        $_POST[$array_key][$key] = $value;
    }

    /**
     * Check if an array $_POST is set.
     *
     * @param string $array_key
     * @param string $key
     *
     * @return bool
     */
    protected function issetArrayPostSuperglobal(string $array_key, string $key) {
        return isset($_POST[strval($array_key)][strval($key)]);
    }

    /**
     * Get value of a $_SERVER.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getServerSuperglobal(string $key) {
        return $_SERVER[strval($key)];
    }

    /**
     * Check if a $_SERVER is set.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function issetServerSperglobal(string $key) {
        return isset($_SERVER[strval($key)]);
    }

    /**
     * Get values of an array $_FILES.
     *
     * @param string   $array_key
     * @param string   $key
     * @param int|null $index
     *
     * @return mixed
     */
    protected function getArrayFilesSuperglobal(string $array_key, string $key, int $index = null) {
        if (is_null($index)) {
            return $_FILES[strval($array_key)][strval($key)];
        }
        return $_FILES[strval($array_key)][strval($key)][intval($index)];
    }

    /**
     * Get keys and values of an array $_POST.
     *
     * @param string $array_key
     *
     * @return array
     */
    protected function getArrayPost(string $array_key) {
        return $_POST[strval($array_key)];
    }

    /**
     * Get keys and values of an array $_FILES.
     *
     * @param string $array_key
     *
     * @return array
     */
    protected function getArrayFiles(string $array_key) {
        return $_FILES[strval($array_key)];
    }
}