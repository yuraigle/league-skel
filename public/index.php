<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

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

// Dispatch
$request  = ServerRequestFactory::fromGlobals();
$response = $dispatcher->dispatch($request);
(new SapiEmitter())->emit($response);
