<?php

namespace App\Component;

use League\Route\Http\Exception\HttpExceptionInterface;
use League\Route\Router as LeagueRouter;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Twig\Environment as TemplateEngine;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AppDispatcher implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly LeagueRouter $router,
        private readonly TemplateEngine $template
    ) {
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function dispatch(Psr7Request $request): Psr7Response
    {
        try {
            return $this->router->dispatch($request);
        } catch (HttpExceptionInterface $e) {
            $httpCode = $e->getStatusCode();
            $html = $this->renderSafe("error/$httpCode.twig", $httpCode);
            return new Response($httpCode, [], $html);
        } catch (Throwable $e) {
            $this->logger->error(
                $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
                ['uri' => $request->getUri()->getPath()]
            );

            $html = $this->renderSafe('error/500.twig', '500 Server Error');
            return new Response(500, [], $html);
        }
    }

    private function renderSafe(string $tpl, string $fallback = ''): string
    {
        try {
            return $this->template->render($tpl);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            $this->logger->error($e->getMessage());
            return $fallback;
        }
    }
}
