<?php

namespace App\Component;

use App\Controller\PagesController;
use App\Controller\AbstractController;
use App\Controller\CitiesController;
use App\Controller\HomeController;
use App\Provider\LoggerServiceProvider;
use App\Provider\RouterServiceProvider;
use App\Provider\TemplateServiceProvider;
use App\Service\CitiesService;
use League\Container\Container as Psr11Container;
use League\Route\Router as LeagueRouter;
use Psr\Log\LoggerAwareInterface;
use Twig\Environment as TemplateEngine;

class AppContainer
{
    public static function initContainer(): Psr11Container
    {
        $container = new Psr11Container();

        $container->addServiceProvider(new LoggerServiceProvider());
        $container->addServiceProvider(new TemplateServiceProvider());
        $container->addServiceProvider(new RouterServiceProvider());

        $container->add(DbConnection::class);

        $container->add(AppDispatcher::class)
            ->addArgument(LeagueRouter::class)
            ->addArgument(TemplateEngine::class);

        // services
        $container->add(CitiesService::class)
            ->addArgument(DbConnection::class);

        // controllers
        $container->add(HomeController::class);

        $container->add(PagesController::class);

        $container->add(CitiesController::class)
            ->addArgument(CitiesService::class);

        // All controllers can use the template engine
        $container
            ->inflector(AbstractController::class)
            ->invokeMethod('setTemplate', [TemplateEngine::class]);

        $container
            ->inflector(LoggerAwareInterface::class)
            ->invokeMethod('setLogger', [\Monolog\Logger::class]);

        return $container;
    }
}
