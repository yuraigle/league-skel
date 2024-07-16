<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;

class AboutController extends AbstractController
{
    /**
     * @param   Psr7Request  $request
     *
     * @return  Psr7Response
     */
    public function index(Psr7Request $request): Psr7Response
    {
        return $this->render("about.twig", ['version' => '1.0.0']);
    }
}
