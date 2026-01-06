<?php
namespace App\Core;

class Request
{
       
		public function getQueryParams()
		{
			return $_GET;
		}

       
		public function getQueryParam($name, $default = null)
		{
			return isset($_GET[$name]) ? $_GET[$name] : $default;
		}
	public function getMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	public function getUri()
	{
		return $_SERVER['REQUEST_URI'];
	}

	public function getBody()
	{
		$input = file_get_contents('php://input');
		$data = json_decode($input, true);
		return $data ? $data : [];
	}

	public function getHeader($name)
	{
		$header = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
		return $_SERVER[$header] ?? null;
	}
}
