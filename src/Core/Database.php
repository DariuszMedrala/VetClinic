<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }

        return self::$instance;
    }

    private static function connect(): PDO
    {
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '5432';
        $name = getenv('POSTGRES_DB') ?: 'vetclinic';
        $user = getenv('POSTGRES_USER') ?: 'vetclinic';
        $password = getenv('POSTGRES_PASSWORD') ?: '';

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $name);

        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $pdo->exec("SET TIME ZONE 'Europe/Warsaw'");

            return $pdo;
        } catch (PDOException $exception) {
            throw new RuntimeException('Nie udało się połączyć z bazą danych: ' . $exception->getMessage(), 0, $exception);
        }
    }
}
