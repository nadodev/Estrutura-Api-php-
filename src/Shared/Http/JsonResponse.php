<?php

declare(strict_types=1);

namespace App\Shared\Http;

final class JsonResponse
{
    public function __construct(
        private mixed $data = null,
        private int $statusCode = 200,
        private array $headers = []
    ) {}

    public function emit(): void
    {
        http_response_code($this->statusCode);

        header('Content-Type: application/json; charset=UTF-8');

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        if ($this->statusCode === 204) {
            return;
        }

        echo json_encode([
            'success' => $this->statusCode < 400,
            'data' => $this->data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function send(
        mixed $data = null,
        int $statusCode = 200,
        array $headers = []
    ): void {
        (new self($data, $statusCode, $headers))->emit();
    }
}