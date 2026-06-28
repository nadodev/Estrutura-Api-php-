<?php

declare(strict_types=1);

namespace App\Shared\Database;

use PDO;

final class Connection
{
    private static ?PDO $connection = null;

    public static function reset(): void
    {
        self::$connection = null;
    }

    public static function pdo(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $driver = strtolower($_ENV['DB_CONNECTION'] ?? 'mysql');
        $database = $_ENV['DB_DATABASE'] ?? 'app_db';

        if ($driver === 'sqlite') {
            $dsn = $database === ':memory:'
                ? 'sqlite::memory:'
                : sprintf('sqlite:%s', $database);

            self::$connection = new PDO($dsn);
        } else {
            $host = $_ENV['DB_HOST'] ?? 'mysql';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $username = $_ENV['DB_USERNAME'] ?? 'app_user';
            $password = $_ENV['DB_PASSWORD'] ?? 'app_password';

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return self::$connection;
    }
}