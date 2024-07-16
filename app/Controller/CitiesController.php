<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CitiesService;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;

class CitiesController extends AbstractController
{
    public function __construct(
        private readonly CitiesService $citiesService
    ) {
    }

    public function index(Psr7Request $request): Psr7Response
    {
        $cities = $this->citiesService->getCities();

        return $this->render("cities.twig", ['cities' => $cities]);
    }
}
