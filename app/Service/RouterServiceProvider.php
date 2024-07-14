<?php

namespace App\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Router as LeagueRouter;
use League\Route\Strategy\ApplicationStrategy;

class RouterServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return $id === LeagueRouter::class;
    }

    public function register(): void
    {
        $strategy = new ApplicationStrategy();
        $strategy->setContainer($this->container);

        $router = new LeagueRouter();
        $router->setStrategy($strategy);

        $this->container->add(LeagueRouter::class, $router);
    }
}
