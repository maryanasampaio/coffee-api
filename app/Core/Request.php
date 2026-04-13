<?php

namespace App\Core;

use App\Exceptions\UnsupportedMediaTypeException;
use App\Exceptions\ValidationException;

class Request
{
    private ?string $rawBody = null;
    private ?array $decodedBody = null;

    public function getQueryParams()
    {
        return $_GET;
    }

    public function getQueryParam($name, $default = null)
    {
        return $_GET[$name] ?? $default;
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getBody(): array
    {
        if ($this->decodedBody !== null) {
            return $this->decodedBody;
        }

        $input = trim($this->getRawBody());
        if ($input === '') {
            $this->decodedBody = [];
            return $this->decodedBody;
        }

        if (!$this->hasJsonContentType()) {
            throw new UnsupportedMediaTypeException('Content-Type must be application/json.');
        }

        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ValidationException('Invalid JSON body.');
        }

        if (!is_array($data)) {
            throw new ValidationException('JSON body must be an object or array.');
        }

        $this->decodedBody = $data;

        return $this->decodedBody;
    }

    public function getHeader($name)
    {
        $normalizedName = strtolower($name);

        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $headerName => $value) {
                if (strtolower($headerName) === $normalizedName) {
                    return $value;
                }
            }
        }

        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$serverKey])) {
            return $_SERVER[$serverKey];
        }

        $contentKey = strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$contentKey] ?? null;
    }

    private function getRawBody(): string
    {
        if ($this->rawBody === null) {
            $this->rawBody = file_get_contents('php://input') ?: '';
        }

        return $this->rawBody;
    }

    private function hasJsonContentType(): bool
    {
        $contentType = $this->getHeader('Content-Type');
        if ($contentType === null) {
            return false;
        }

        $mimeType = strtolower(trim(explode(';', $contentType)[0]));
        return $mimeType === 'application/json';
    }
}
