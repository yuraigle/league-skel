<?php

namespace App\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Plates\Engine as TemplateEngine;

class TemplateServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return $id === TemplateEngine::class;
    }

    public function register(): void
    {
        $root = __DIR__ . '/../..';
        $template = new TemplateEngine($root . '/templates', 'tpl');
        $template->addFolder('layout', $root . '/templates/layout');
        $this->container->add(TemplateEngine::class, $template);
    }
}
