<?php

namespace App\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment as TemplateEngine;

class TemplateServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return $id === TemplateEngine::class;
    }

    public function register(): void
    {
        $root = __DIR__ . '/../..';

        $loader = new \Twig\Loader\FilesystemLoader($root . '/templates');
        $twig   = new \Twig\Environment($loader, [
            'cache' => $root . '/cache',
        ]);
        $this->container->add(TemplateEngine::class, $twig);
    }
}
