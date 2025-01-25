<?php

namespace App\Service;

use App\Component\DbConnection;
use Exception;

class AuthService
{
    public function __construct(
        private readonly DbConnection $db
    ) {
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