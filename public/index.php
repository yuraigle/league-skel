<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\AboutController;
use App\Controller\HomeController;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router as LeagueRouter;
use Twig\Environment as TemplateEngine;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

$_ENV['ROOT'] = dirname(__DIR__);
$dotenv       = Dotenv\Dotenv::createImmutable($_ENV['ROOT']);
$dotenv->load();

$container = App\Component\AppContainer::initContainer();
$router    = $container->get(LeagueRouter::class);
$log       = $container->get(Monolog\Logger::class);
$template  = $container->get(TemplateEngine::class);

// Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [AboutController::class, 'index']);
$router->get('/hello', [HomeController::class, 'hello']);
$router->get('/hello/{name}', [HomeController::class, 'hello']);

// Dispatch
$request = ServerRequestFactory::fromGlobals();

try {
    $response = $router->dispatch($request);
} catch (NotFoundException $e) {
    try {
        $html = $template->render('error/404.twig');
    } catch (LoaderError | RuntimeError | SyntaxError) {
        $html = "404 Not Found";
    }

    $response = new HtmlResponse($html, 404);
} catch (Throwable $e) {
    $log->error(
        $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
        ['uri' => $request->getUri()->getPath()]
    );

    try {
        $html = $template->render('error/500.twig');
    } catch (LoaderError | RuntimeError | SyntaxError) {
        $html = "500 Internal Server Error";
    }

    $response = new HtmlResponse($html, 500);
}

(new SapiEmitter())->emit($response);
