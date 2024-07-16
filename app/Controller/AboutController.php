<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\DbConnection;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;

class AboutController extends AbstractController
{
    public function __construct(
        private readonly DbConnection $db
    ) {
    }

    public function index(Psr7Request $request): Psr7Response
    {
        $row = $this->db->getConn()->query('select @@version as version');
        $version = $row->fetch_assoc()['version'];

        return $this->render("about.twig", ['version' => $version]);
    }
}
