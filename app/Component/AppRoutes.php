<?php

namespace App\Component;

use App\Controller\AuthController;
use App\Controller\CitiesController;
use App\Controller\HomeController;
use App\Controller\PagesController;
use League\Route\Router as LeagueRouter;

class AppRoutes
{
    public function registerRoutes(LeagueRouter $router): void
    {
        $router->get('/', [HomeController::class, 'home']);

        $router->get('/login', [AuthController::class, 'login']);
        $router->post('/login', [AuthController::class, 'loginPost']);
        $router->get('/logout', [AuthController::class, 'logout']);

        $router->get('/terms', [PagesController::class, 'terms']);
        $router->get('/cities', [CitiesController::class, 'index']);

        $router->get('/secure', [PagesController::class, 'secured']);
    }
}
