<?php

namespace App\Component;

use App\Controller\CitiesController;
use App\Controller\HomeController;
use App\Controller\PagesController;
use League\Route\Router as LeagueRouter;

class AppRoutes
{
    public function registerRoutes(LeagueRouter $router): void
    {
        $router->get('/', [HomeController::class, 'home']);
        $router->get('/about', [PagesController::class, 'about']);
        $router->get('/terms', [PagesController::class, 'terms']);
        $router->get('/cities', [CitiesController::class, 'index']);
    }
}
