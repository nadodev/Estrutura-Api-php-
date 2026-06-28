<?php

declare(strict_types=1);

namespace App\Shared\Http;

final readonly class Request
{
    public function __construct(
        public array $query,
        public array $body,
        public array $server,
        public array $headers
    ) {}

    public static function capture(): self
    {
        $rawBody = file_get_contents('php://input');

        $body = json_decode($rawBody ?: '', true);

        if (! is_array($body)) {
            $body = $_POST;
        }

        return new self(
            query: $_GET,
            body: $body,
            server: $_SERVER,
            headers: self::headers()
        );
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $authorization = $this->headers['Authorization']
            ?? $this->headers['authorization']
            ?? null;

        if (! $authorization) {
            return null;
        }

        if (! str_starts_with($authorization, 'Bearer ')) {
            return null;
        }

        return substr($authorization, 7);
    }

    private static function headers(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }

        return $headers;
    }
}