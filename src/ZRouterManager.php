<?php

namespace ZRouter;

use Closure;

trait ZRouterManager
{
    protected array $routes = [];
    public $error = false;
    protected string $theNamespace;
    protected array $theMiddlewares = [];

    public function addRoute(string $route, string|Closure $callable, string $name = '', array $middlewares, string $method)
    {
        $routePattern = '/^' . str_replace('/', '\/', $route) . '$/';
        if (is_callable($callable)) {
            return $this->addCallable($route, $method, $callable, $name, empty($middlewares) ? $this->theMiddlewares : $middlewares);
        } elseif (preg_match('/([a-zA-Z]):([a-zA-Z0-9])/', $callable)) {
            $controller = explode(':', $callable);
            // return $this->dd($callable);
            return $this->routes[$routePattern] = array_filter(['method' => $method, 'controller' => $this->theNamespace . '\\' . trim($controller[0], '\\'), 'controller_method' => $controller[1], 'name' => $name, 'middlewares' => empty($middlewares) ? $this->theMiddlewares : $middlewares]);
        }
    }

    protected function addCallable(string $route, string $method, callable $callable, string $name = '', array $middlewares = [])
    {
        $routePattern = '/^' . str_replace('/', '\/', $route) . '$/';
        $this->routes[$routePattern] = array_filter(['method' => $method, 'callable' => $callable, 'name' => $name, 'middlewares' => $middlewares]);
    }

    public function run()
    {
        $request = file_get_contents('php://input');
        foreach ($this->routes as $route => $key) {
            $pattern = '/{(.*?)}/';
            $routePattern = preg_replace($pattern, '(.*?)', $route);

            if (preg_match($routePattern, $this->getUri(), $matches)) {
                if ($this->httpMethod() !== $this->routes[$route]['method']) {
                    return $this->error = [
                        'errcode' => 405,
                        'message' => 'Not allowed'
                    ];
                }

                unset($matches[0]);
                preg_match_all($pattern, $route, $matches1);
                $args = array_combine($matches1[1], $matches);

                if (isset($request)) {
                    $request = json_decode($request, true);
                    $args = array_merge($args, $request);
                }

                return $this->execute($this->routes[$route], $args);
            }
        }

        return $this->error = [
            'errcode' => 404,
            'message' => 'Page not found!'
        ];
    }


    protected function httpMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    protected function getUri()
    {
        return $_SERVER['REQUEST_URI'];
    }
}
