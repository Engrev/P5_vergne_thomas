<?php
namespace App\Core;

/**
 * Class Route
 * @package App\Core
 *
 * Represents a route.
 */
class Route
{
    /**
     * @var string The path.
     */
    private $path;
    /**
     * @var mixed The closure.
     */
    private $callable;
    /**
     * @var array URL matches.
     */
    private $matches = [];
    /**
     * @var array URL constraints.
     */
    private $params = [];

    /**
     * Route constructor.
     *
     * @param string $path
     * @param mixed $callable Function.
     */
    public function __construct($path, $callable)
    {
        $this->path = trim($path, '/');
        $this->callable = $callable;
    }

    /**
     * Checks if the URL typed matches one of the saved routes by transforming the URL.
     * e.g : get('/posts/:id-:slug').
     * If so, matches are saved.
     * And check if the match is not in the constraint parameters.
     *
     * @param string $url
     *
     * @return bool
     */
    public function match($url)
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regexp = "#^$path$#i";
        if (!preg_match($regexp, $url, $matches)) {
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    /**
     * If the match is in the constraint parameters, a regexp with match is returned.
     * The parentheses are useful for capturing in $matches in match() method.
     *
     * @param array $match
     *
     * @return string
     */
    private function paramMatch($match)
    {
        if (isset($this->params[$match[1]])) {
            return '('.$this->params[$match[1]].')';
        }
        return '([^/]+)';
    }

    /**
     * Call the method related to matches.
     *
     * @return mixed
     */
    public function call()
    {
        if (is_string($this->callable)) {
            $params = explode('#', $this->callable);
            $controller = 'App\\Controllers\\'.$params[0].'Controller';
            $Controller = new $controller();
            return call_user_func_array([$Controller, $params[1]], $this->matches);
        } else {
            return call_user_func_array($this->callable, $this->matches);
        }
    }

    /**
     * Fluent.
     * Save constraints.
     * '(?:' is useful for parentheses in $regexp in this method. Like that, the parentheses are not captivating.
     *
     * @param string $param
     * @param string $regexp
     *
     * @return $this For others with().
     */
    public function with($param, $regexp)
    {
        $this->params[$param] = str_replace('(', '(?:', $regexp);
        return $this;
    }

    /**
     * Displays the URL corresponding to the name.
     *
     * @param array $params
     *
     * @return string|string[]
     */
    public function getUrl($params)
    {
        $path = $this->path;
        foreach ($params as $key => $value) {
            $path = str_replace(":$key", $value, $path);
        }
        return $path;
    }
}