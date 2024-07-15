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
        $response = new HtmlResponse($template->render('error/404.twig'), 404);
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
        $response = new HtmlResponse("404 Not Found", 404);
    }
} catch (Throwable $e) {
    $log->error(
        $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
        ['uri' => $request->getUri()->getPath()]
    );

    try {
        $response = new HtmlResponse($template->render('error/500.twig'), 404);
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
        $response = new HtmlResponse("500 Internal Server Error", 500);
    }
}

(new SapiEmitter())->emit($response);
