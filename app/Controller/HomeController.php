<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Psr7Response;

class HomeController extends AbstractController
{
    public function home(): Psr7Response
    {
        $name = $this->getRequest()->getQueryParams()["name"] ?? "World";
        return $this->render("home.twig", ['name' => $name]);
    }
}
