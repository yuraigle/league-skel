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
        $templatesPath = $_ENV['ROOT'] . '/templates';
        $cachePath     = $_ENV['ROOT'] . '/cache';
        $loader        = new \Twig\Loader\FilesystemLoader($templatesPath);
        $twig          = new TemplateEngine($loader, ['cache' => $cachePath]);
        $this->container->add(TemplateEngine::class, $twig);
    }
}
