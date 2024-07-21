<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Psr7Response;

/**
 * Static pages
 */
class PagesController extends AbstractController
{
    public function about(): Psr7Response
    {
        return $this->render("pages/about.twig");
    }

    public function terms(): Psr7Response
    {
        return $this->render(
            "pages/terms.twig",
            ["site_name" => $_ENV['SITE_NAME']]
        );
    }
}
