<?php

namespace App\Component;

use App\Controller\AbstractController;
use App\Controller\AuthController;
use App\Controller\CitiesController;
use App\Controller\HomeController;
use App\Controller\PagesController;
use App\Provider\DatabaseServiceProvider;
use App\Provider\LoggerServiceProvider;
use App\Provider\RequestServiceProvider;
use App\Provider\RouterServiceProvider;
use App\Provider\TemplateServiceProvider;
use App\Service\AuthService;
use App\Service\CitiesService;
use League\Container\Container as Psr11Container;
use League\Route\Router as LeagueRouter;
use Monolog\Logger;
use PDO;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Psr\Log\LoggerAwareInterface;
use Twig\Environment as TemplateEngine;

class AppContainer
{
    public static function initContainer(): Psr11Container
    {
        $container = new Psr11Container();

        $container->addServiceProvider(new LoggerServiceProvider());
        $container->addServiceProvider(new TemplateServiceProvider());
        $container->addServiceProvider(new RequestServiceProvider());
        $container->addServiceProvider(new RouterServiceProvider());
        $container->addServiceProvider(new DatabaseServiceProvider());

        $container->add(AppDispatcher::class)
            ->addArgument(LeagueRouter::class)
            ->addArgument(TemplateEngine::class);

        // services
        $container->add(AuthService::class)->addArgument(PDO::class);
        $container->add(CitiesService::class)->addArgument(PDO::class);

        // controllers
        $container->add(HomeController::class);
        $container->add(PagesController::class);
        $container->add(AuthController::class)->addArgument(AuthService::class);
        $container->add(CitiesController::class)->addArgument(CitiesService::class);

        // All controllers can use the template engine
        $container->inflector(AbstractController::class)
            ->invokeMethod('setRequest', [Psr7Request::class])
            ->invokeMethod('setTemplate', [TemplateEngine::class]);

        $container->inflector(LoggerAwareInterface::class)
            ->invokeMethod('setLogger', [Logger::class]);

        return $container;
    }
}
