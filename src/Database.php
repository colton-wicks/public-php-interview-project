<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use App\Exceptions\DatabaseException;

class Database
{
    /**
     * Builds a PDO connection from environment variables, falling back to
     * local development defaults (MySQL on 127.0.0.1, root with no password).
     *
     * Supported variables: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD
     * @throws DatabaseException If the connection fails
     */
    public static function getConnection(): PDO
    {
        $host     = self::env('DB_HOST', '127.0.0.1');
        $port     = self::env('DB_PORT', '3306');
        $database = self::env('DB_NAME', 'php_test');
        $username = self::env('DB_USER', 'root');
        $password = self::env('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            return new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            // Name the target so the log shows which connection failed (never log the password)
            throw new DatabaseException(
                "Could not connect to database '{$database}' on {$host}:{$port} as '{$username}'",
                0,
                $e
            );
        }
    }

    /**
     * Reads an environment variable, returning the default when it is unset.
     * An explicitly empty value (e.g. DB_PASSWORD=) is respected.
     */
    private static function env(string $key, string $default): string
    {
        $value = getenv($key);

        return $value === false ? $default : $value;
    }
}
