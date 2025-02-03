<?php

namespace App\Service;

use PDO;

class CitiesService
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function getCities(): array
    {
        $sql = <<<SQL
            select name, state_code, population
            from `cities`
            where population > 1000000
            order by rand() desc limit 5
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
