<?php

namespace App\Component;

class DbConnection
{
    private static \mysqli | null $conn = null;

    public function getConn(): \mysqli
    {
        if (self::$conn) {
            return self::$conn;
        }

        self::$conn = new \mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            $_ENV['DB_NAME']
        );

        return self::$conn;
    }
}
