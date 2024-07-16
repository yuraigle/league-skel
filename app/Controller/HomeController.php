<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;

class HomeController extends AbstractController
{
    public function hello(Psr7Request $request): Psr7Response
    {
        $name = $request->getAttribute('name', 'World');

        return $this->render("hello.twig", ['name' => $name]);
    }
}
