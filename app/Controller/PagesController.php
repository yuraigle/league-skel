<?php

declare(strict_types=1);

namespace App\Controller;

use League\Route\Http\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface as Psr7Response;

/**
 * Static pages
 */
class PagesController extends AbstractController
{
    public function terms(): Psr7Response
    {
        return $this->render(
            "pages/terms.twig",
            ["site_name" => $_ENV['SITE_NAME']]
        );
    }

    /**
     * @throws UnauthorizedException
     */
    public function dashboard(): Psr7Response
    {
        $auth = $this->getAuth();
        if (!$auth) {
            throw new UnauthorizedException();
        }

        return $this->render("pages/dashboard.twig");
    }
}
