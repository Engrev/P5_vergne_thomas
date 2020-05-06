<?php
namespace App\Core;
use App\Exceptions\RouterException;

/**
 * Class Router
 * @package App\Core
 */
class Router
{
    /**
     * @var string The path.
     */
    private $url;
    /**
     * @var array All routes (instances) with the HTTP method as a key.
     */
    private $routes = [];
    /**
     * @var array URL names.
     */
    private $namedRoutes = [];

    /**
     * Router constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Call the page with the HTTP GET method.
     *
     * @param string $path
     * @param mixed $callable Function to call when the URL is captured.
     * @param null $name string
     *
     * @return Route
     */
    public function get($path, $callable, $name = null)
    {
        return $this->add($path, $callable, $name, 'GET');
    }

    /**
     * Call the page with the HTTP POST method.
     *
     * @param string $path
     * @param mixed $callable Function to call when the URL is captured.
     * @param null $name
     *
     * @return Route
     */
    public function post($path, $callable, $name = null)
    {
        return $this->add($path, $callable, $name, 'POST');
    }

    /**
     * Initializes a Route instance.
     * Returns an instance of Route to be able to use the with() method in the index.php.
     *
     * @param string $path
     * @param mixed $callable
     * @param string $name
     * @param string $method
     *
     * @return Route
     */
    private function add($path, $callable, $name, $method)
    {
        $Route = new Route($path, $callable);
        $this->routes[$method][] = $Route;
        if (is_string($callable) && is_null($name)) {
            $name = $callable;
        }
        if ($name) {
            $this->namedRoutes[$name] = $Route;
        }
        return $Route;
    }

    /**
     * Checks if the URL typed matches one of the saved routes with the match() method.
     * If so, the closure is called.
     *
     * @return mixed
     * @throws RouterException
     */
    public function run()
    {
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            throw new RouterException('REQUEST_METHOD does not exist');
        }
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }
        throw new RouterException('No matching routes');
    }

    /**
     * Displays the URL corresponding to the name.
     *
     * @param string $name
     * @param array $params
     *
     * @return mixed
     * @throws RouterException
     */
    public function url($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException('No route matches this name');
        }
        return $this->namedRoutes[$name]->getUrl($params);
    }
}