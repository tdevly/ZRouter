<?php

namespace ZRouter;

class ZRouter
{
    use ZRouterManager;

    public function __construct(
        public string $url
    ) {
    }

    // public function get(string $route, string $controller, string|array $method = '', array|string $middlewares = [], string $name = '')
    public function get(string $route, string|callable $callable, string $name = '', array $middlewares = [])
    {        
        return self::addRoute($route, $callable, $name, $middlewares, strtoupper(__FUNCTION__));
    }

    public function post(string $route, string|callable $callable, string $name = '', array $middlewares = [])
    {        
        return self::addRoute($route, $callable, $name, $middlewares, strtoupper(__FUNCTION__));
    }

    public function put(string $route, string|callable $callable, string $name = '', array $middlewares = [])
    {        
        return self::addRoute($route, $callable, $name, $middlewares, strtoupper(__FUNCTION__));
    }

    public function delete(string $route, string|callable $callable, string $name = '', array $middlewares = [])
    {        
        return self::addRoute($route, $callable, $name, $middlewares, strtoupper(__FUNCTION__));
    }

    public function redirect($to)
    {
        if (filter_var($to, FILTER_VALIDATE_URL)) {
            return header('Location: ' . $to . '?utm_source=' . $this->url);
        }

        foreach ($this->routes as $route => $key) {
            if (isset($key['name']) && $key['name'] == $to) {
                return header('Location: ' . str_replace('\/', '/', substr($route, 2, -2)));
            }
        }

        return header('Location: ' . rtrim($this->url, '/') . '/' . ltrim($to, '/'));
    }

    public function namespace(string $namespace){
        $this->theNamespace = trim($namespace, '\\');
    }
    public function middleware(array|null $middleware){
        $this->theMiddlewares = $middleware ? $middleware : [];
    }

    private function execute($route, $args = [])
    {
        if (!empty($route['middlewares'])) {
            foreach ($route['middlewares'] as $middleware) {
                if (!class_exists($middleware)) {
                    throw new \Exception("The middleware {$middleware} class don't exists.");
                }

                $middleware = new $middleware();

                if (method_exists($middleware, 'handle')) {
                        $result = call_user_func([$middleware, 'handle']);
                    if (!$result) {
                        if (method_exists($middleware, 'callback')) {
                            return call_user_func([$middleware, 'callback'], $this);
                        }
                        return header('Location: /');
                    }
                }
            }
        }

        if(isset($route['callable'])){
            return call_user_func($route['callable'], $args);
        }

        if (!class_exists($route['controller'])) {
            return $this->error = [
                'errcode' => 500,
                'message' => "The {$route['controller']} Controller don't exists!"
            ];
        }

        $reflector = new \ReflectionClass($route['controller']);

        if (!$reflector->hasMethod($route['controller_method'])) {
            return $this->error = [
                'errcode' => 500,
                'message' => "The method {$route['controller_method']} don't exists in {$route['controller']} class."
            ];
        }

        $numberOfParameters = $reflector->getConstructor()->getNumberOfParameters();
        $injectZRouter = false;
        if ($numberOfParameters) {
            $parameters = $reflector->getConstructor()->getParameters();

            for ($i = 0; $i < $numberOfParameters; $i++) {
                if ($parameters[$i]->name == 'zrouter') {
                    $injectZRouter = true;
                }
            }
        }

        $instance = $injectZRouter ? new $route['controller']($this) : new $route['controller']();
        return call_user_func([$instance, $route['controller_method']], $args);
    }
}
