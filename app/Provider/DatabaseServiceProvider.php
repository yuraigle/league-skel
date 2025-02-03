<?php

namespace App\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use PDO;
use PDOException;
use RuntimeException;

class DatabaseServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return $id === PDO::class;
    }

    public function register(): void
    {
        try {
            $conn = new PDO($_ENV["DB_CONN"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $this->container->add(PDO::class, $conn);
        } catch (PDOException $e) {
            throw new RuntimeException("Connection failed: " . $e->getMessage());
        }
    }


}