<?php

namespace App\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment as TemplateEngine;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TemplateServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return $id === TemplateEngine::class;
    }

    public function register(): void
    {
        $templatesPath = $_ENV['ROOT'] . '/templates';
        $cachePath = $_ENV['DEV_MODE'] ? false : $_ENV['ROOT'] . '/cache';
        $loader = new FilesystemLoader($templatesPath);
        $twig = new TemplateEngine($loader, ['cache' => $cachePath]);

        $contents = file_get_contents($_ENV['ROOT'] . '/public/dist/.vite/manifest.json');
        $manifest = json_decode($contents, true);
        $twig->addFunction(new TwigFunction("asset", function($name) use ($manifest) {
            foreach ($manifest as $entry) {
                if ($entry['name'] === $name) {
                    return '/dist/' . $entry['file'];
                }
            }
            return null;
        }));

        $this->container->add(TemplateEngine::class, $twig);
    }
}
