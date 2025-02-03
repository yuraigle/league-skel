<?php

namespace App\Service;

use Ahc\Jwt\JWT;
use Exception;
use PDO;

class AuthService
{
    private static JWT $jwt;

    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public static function generateJwt(array $payload): string
    {
        return self::getJwt()->encode($payload);
    }

    private static function getJwt(): JWT
    {
        if (!isset(self::$jwt)) {
            self::$jwt = new JWT($_ENV['COOKIE_SECRET'], 'HS256', $_ENV['COOKIE_LIFETIME']);
        }

        return self::$jwt;
    }

    public static function parseJwt(string $token): array
    {
        return self::getJwt()->decode($token);
    }

    /**
     * @throws Exception
     */
    public function authenticate(string $user, string $pass): array
    {
        $sql = "select id, username, password from `users` where username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user]);

        if ($row = $stmt->fetch()) {
            if (password_verify($pass, $row['password'])) {
                return ['id' => $row['id'], 'username' => $row['username']];
            }
        }

        throw new Exception("Wrong credentials");
    }

    /**
     * @throws Exception
     */
    public function createTestUser(string $username, string $password): int
    {
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "insert into `users` (username, password) values (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $passHash]);

        return $this->pdo->lastInsertId();
    }
}
