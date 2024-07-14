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

$container = App\Component\AppContainer::initContainer();
$router    = $container->get(LeagueRouter::class);
$log       = $container->get(Monolog\Logger::class);

// Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [AboutController::class, 'index']);
$router->get('/hello', [HomeController::class, 'hello']);
$router->get('/hello/{name}', [HomeController::class, 'hello']);

// Dispatch
try {
    $request  = ServerRequestFactory::fromGlobals();
    $response = $router->dispatch($request);
} catch (NotFoundException $e) {
    $html     = $container->get(League\Plates\Engine::class)->render('error/404');
    $response = new HtmlResponse($html, 404);
} catch (Throwable $e) {
    $log->error($e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);

    $html     = $container->get(League\Plates\Engine::class)->render('error/500');
    $response = new HtmlResponse($html, 500);
}

(new SapiEmitter())->emit($response);
