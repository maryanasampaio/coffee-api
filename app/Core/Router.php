<?php
namespace App\Core;

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
			'auth' => $auth
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
			$pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $route['pattern']);
			$pattern = '#^' . $pattern . '$#';
			if ($route['method'] === $requestMethod && preg_match($pattern, $uri, $matches)) {
				if ($route['auth']) {
					\App\Core\Auth::check();
				}
				list($controller, $method) = explode('@', $route['handler']);
				$controllerInstance = new $controller();
				$params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
				$reflection = new \ReflectionMethod($controllerInstance, $method);
				$args = [];
				foreach ($reflection->getParameters() as $i => $param) {
					if ($param->getType() && $param->getType()->getName() === 'App\\Core\\Request') {
						$args[] = new \App\Core\Request();
					} elseif (!empty($params)) {
						$next = array_shift($params);
						if ($param->getType() && $param->getType()->getName() === 'int') {
							$args[] = (int)$next;
						} else {
							$args[] = $next;
						}
					}
				}
				return call_user_func_array([$controllerInstance, $method], $args);
			}
		}
		http_response_code(404);
		echo json_encode(['error' => 'Not Found']);
	}
}
