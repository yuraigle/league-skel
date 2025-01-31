<?php

namespace App\Service;

use Ahc\Jwt\JWT;
use App\Component\DbConnection;
use Exception;

class AuthService
{
    private static JWT $jwt;

    public function __construct(
        private readonly DbConnection $db
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
        $sql = "select id, username from `users` where username = ? and password = ?";
        $stmt = $this->db->getConn()->prepare($sql);
        $stmt->bind_param("ss", $user, $pass);
        $stmt->execute();

        if ($stmt->error) {
            throw new Exception($stmt->error);
        }

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Wrong credentials");
        }

        $row = $result->fetch_assoc();

        if ($row && $row['id']) {
            return $row;
        }

        throw new Exception("No user found");
    }
}
