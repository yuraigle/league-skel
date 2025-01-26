<?php

namespace App\Provider;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;

class RequestServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return $id === Psr7Request::class;
    }

    public function register(): void
    {
        $psr17 = new Psr17Factory();
        $creator = new ServerRequestCreator($psr17, $psr17, $psr17, $psr17);
        $request = $creator->fromGlobals();

        $this->container->add(Psr7Request::class, $request);
    }
}
