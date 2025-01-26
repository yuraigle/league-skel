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
use Twig\Error\Error as TwigError;

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
            return $this->renderHttpException($e->getStatusCode());
        } catch (Throwable $e) {
            $this->logger->error(
                $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
                ['uri' => $request->getUri()->getPath()]
            );

            return $this->renderHttpException(500);
        }
    }

    private function renderHttpException(int $code): Response
    {
        try {
            $html = $this->template->render("error/$code.twig");
            return new Response($code, [], $html);
        } catch (TwigError $e) {
            $this->logger->error($e->getMessage());
            return new Response($code, [], "HTTP $code");
        }
    }
}
