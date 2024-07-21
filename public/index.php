<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\PagesController;
use App\Controller\CitiesController;
use App\Controller\HomeController;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router as LeagueRouter;

// Environment
$_ENV['ROOT'] = dirname(__DIR__);
$dotenv       = Dotenv\Dotenv::createImmutable($_ENV['ROOT']);
$dotenv->load();

// App init
$container  = App\Component\AppContainer::initContainer();
$router     = $container->get(LeagueRouter::class);
$dispatcher = $container->get(App\Component\AppDispatcher::class);

// Routes
$router->get('/', [HomeController::class, 'hello']);
$router->get('/hello', [HomeController::class, 'hello']);
$router->get('/hello/{name}', [HomeController::class, 'hello']);
$router->get('/about', [PagesController::class, 'about']);
$router->get('/terms', [PagesController::class, 'terms']);
$router->get('/cities', [CitiesController::class, 'index']);

// Dispatch
$request  = ServerRequestFactory::fromGlobals();
$response = $dispatcher->dispatch($request);
(new SapiEmitter())->emit($response);
