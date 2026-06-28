<?php

declare(strict_types=1);

namespace App\Shared\Http;

final class Cors
{
    public static function handle(): void
    {
        $frontendUrl = $_ENV['FRONTEND_URL'] ?? '*';

        header('Access-Control-Allow-Origin: ' . $frontendUrl);
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Vary: Origin');

        if ($frontendUrl !== '*') {
            header('Access-Control-Allow-Credentials: true');
        }
    }
}