<?php

namespace App\Component;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class DbConnection implements LoggerAwareInterface
{
    private static \mysqli | null $conn = null;
    private LoggerInterface $logger;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getConn(): \mysqli
    {
        if (self::$conn) {
            return self::$conn;
        }

        try {
            self::$conn = new \mysqli(
                $_ENV['DB_HOST'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                $_ENV['DB_NAME']
            );
        } catch (\Throwable $e) {
            $this->logger->critical("DB DOWN: " . $e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }

        return self::$conn;
    }
}
