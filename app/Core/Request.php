<?php

namespace App\Core;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UnsupportedMediaTypeException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationException;

class Request
{
    private ?string $rawBody = null;
    private ?array $decodedBody = null;
    private ?int $authenticatedUserId = null;

    public function getQueryParams()
    {
        return $_GET;
    }

    public function getQueryParam($name, $default = null)
    {
        return $_GET[$name] ?? $default;
    }

    public function getPositiveIntQueryParam(string $name, ?int $default = null, ?string $invalidMessage = null): int
    {
        $value = $this->getQueryParam($name);
        if ($value === null || $value === '') {
            if ($default !== null) {
                return $default;
            }

            throw new ValidationException($invalidMessage ?? sprintf('Query param "%s" is required.', $name));
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 1) {
            throw new ValidationException($invalidMessage ?? sprintf('Query param "%s" must be a positive integer.', $name));
        }

        return (int) $value;
    }

    public function getDateQueryParam(string $name, ?string $default = null, ?string $requiredMessage = null, ?string $invalidMessage = null): string
    {
        $value = $this->getQueryParam($name);
        if ($value === null || trim((string) $value) === '') {
            if ($default !== null) {
                return $default;
            }

            throw new ValidationException($requiredMessage ?? sprintf('Query param "%s" is required.', $name));
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', (string) $value);
        $errors = \DateTimeImmutable::getLastErrors();
        $hasParsingErrors = is_array($errors)
            && ($errors['warning_count'] > 0 || $errors['error_count'] > 0);

        if ($date === false || $hasParsingErrors || $date->format('Y-m-d') !== $value) {
            throw new ValidationException($invalidMessage ?? sprintf('Query param "%s" must be a valid date in Y-m-d format.', $name));
        }

        return $value;
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function setAuthenticatedUserId(int $authenticatedUserId): void
    {
        $this->authenticatedUserId = $authenticatedUserId;
    }

    public function getAuthenticatedUserId(): ?int
    {
        return $this->authenticatedUserId;
    }

    public function requireAuthenticatedUserId(): int
    {
        if ($this->authenticatedUserId === null) {
            throw new UnauthorizedException();
        }

        return $this->authenticatedUserId;
    }

    public function ensureAuthenticatedUserOwns(int $userId): void
    {
        if ($this->requireAuthenticatedUserId() !== $userId) {
            throw new ForbiddenException();
        }
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

    public function requireBodyFields(array $fields, ?string $message = null): array
    {
        $data = $this->getBody();

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null) {
                throw new ValidationException($message ?? sprintf('Field "%s" is required.', $field));
            }

            if (is_string($data[$field]) && trim($data[$field]) === '') {
                throw new ValidationException($message ?? sprintf('Field "%s" is required.', $field));
            }
        }

        return $data;
    }

    public function getPositiveIntBodyField(string $name, ?int $default = null, ?string $invalidMessage = null): int
    {
        $data = $this->getBody();

        if (!array_key_exists($name, $data) || $data[$name] === null || $data[$name] === '') {
            if ($default !== null) {
                return $default;
            }

            throw new ValidationException($invalidMessage ?? sprintf('Field "%s" must be a positive integer.', $name));
        }

        if (filter_var($data[$name], FILTER_VALIDATE_INT) === false || (int) $data[$name] < 1) {
            throw new ValidationException($invalidMessage ?? sprintf('Field "%s" must be a positive integer.', $name));
        }

        return (int) $data[$name];
    }

    public function requireEmailBodyField(string $name, ?string $requiredMessage = null, ?string $invalidMessage = null): string
    {
        $data = $this->requireBodyFields([$name], $requiredMessage);
        $email = (string) $data[$name];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException($invalidMessage ?? sprintf('Field "%s" must be a valid email.', $name));
        }

        return $email;
    }

    public function getOptionalEmailBodyField(string $name, ?string $invalidMessage = null): ?string
    {
        $data = $this->getBody();
        if (!array_key_exists($name, $data) || $data[$name] === null || $data[$name] === '') {
            return null;
        }

        $email = (string) $data[$name];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException($invalidMessage ?? sprintf('Field "%s" must be a valid email.', $name));
        }

        return $email;
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
