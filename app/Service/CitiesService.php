<?php

namespace App\Service;

use App\Component\DbConnection;

class CitiesService
{
    public function __construct(
        private readonly DbConnection $db
    ) {
    }

    public function getCities(): array
    {
        $sql = <<<SQL
            select name, state_code, population
            from `geo_cities`
            where population > 1000000
            order by rand() desc limit 5
        SQL;

        $res = $this->db->getConn()->query($sql);

        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
