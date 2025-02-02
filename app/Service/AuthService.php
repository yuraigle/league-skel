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
        $sql = "select id, username, password from `users` where username = ?";
        $stmt = $this->db->getConn()->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();

        if ($stmt->error) {
            throw new Exception($stmt->error);
        }

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
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
        if ($this->isUsernameTaken($username)) {
            throw new Exception("Username already taken.");
        }

        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "insert into `users` (username, password) values (?, ?)";
        $stmt = $this->db->getConn()->prepare($sql);
        $stmt->bind_param("ss", $username, $passHash);
        $stmt->execute();

        return $stmt->insert_id;
    }

    private function isUsernameTaken(string $username): bool
    {
        $sql = "select id from `users` where username = ?";
        $stmt = $this->db->getConn()->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    public function isUsersTableEmpty(): bool
    {
        $sql = "select count(*) as cnt from `users`";
        $stmt = $this->db->getConn()->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['cnt'] === 0;
        }

        return false;
    }
}
