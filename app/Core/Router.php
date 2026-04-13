<?php

namespace App\Core;

use App\Exceptions\NotFoundException;

class Router
{
    private $routes = [];
    private $prefix = '';

    public function add($method, $pattern, $handler, $auth = false)
    {
        $uri = $this->prefix . $pattern;
        $this->routes[] = [
            'method' => $method,
            'pattern' => $uri,
            'handler' => $handler,
            'auth' => $auth,
        ];
    }

    public function group(string $prefix, callable $callback)
    {
        $previousPrefix = $this->prefix;
        $this->prefix .= $prefix;
        $callback($this);
        $this->prefix = $previousPrefix;
    }

    public function dispatch($requestMethod, $requestUri)
    {
        $uri = parse_url($requestUri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $matches = $this->matchRoute($route, $requestMethod, $uri);

            if ($matches === null) {
                continue;
            }

            return $this->dispatchMatchedRoute($route, $matches);
        }

        throw new NotFoundException('Route not found.');
    }

    private function matchRoute(array $route, string $requestMethod, string $uri): ?array
    {
        if ($route['method'] !== $requestMethod) {
            return null;
        }

        $pattern = preg_replace('#\{([a-zA-Z_]\w*)\}#', '(?P<$1>[^/]+)', $route['pattern']);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return null;
        }

        return $matches;
    }

    private function dispatchMatchedRoute(array $route, array $matches)
    {
        $request = new Request();

        if ($route['auth']) {
            $request->setAuthenticatedUserId(Auth::check());
        }

        [$controller, $method] = explode('@', $route['handler']);
        $controllerInstance = new $controller();
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        $reflection = new \ReflectionMethod($controllerInstance, $method);
        $args = $this->buildArguments($reflection, $params, $request);

        return call_user_func_array([$controllerInstance, $method], $args);
    }

    private function buildArguments(\ReflectionMethod $reflection, array $params, Request $request): array
    {
        $args = [];

        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();
            $typeName = $type ? $type->getName() : null;

            if ($typeName === Request::class) {
                $args[] = $request;
                continue;
            }

            if (empty($params)) {
                continue;
            }

            $next = array_shift($params);
            $args[] = $typeName === 'int' ? (int) $next : $next;
        }

        return $args;
    }
}
