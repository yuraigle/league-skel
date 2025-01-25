<?php

namespace App\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment as TemplateEngine;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class AbstractController implements LoggerAwareInterface
{
    protected TemplateEngine $template;
    protected LoggerInterface $logger;
    protected Psr7Request $request;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setTemplate(TemplateEngine $template): void
    {
        $this->template = $template;
    }

    public function getRequest(): Psr7Request
    {
        return $this->request;
    }

    public function setRequest(Psr7Request $request): void
    {
        $this->request = $request;
    }

    protected function render($view, $args = []): Psr7Response
    {
        try {
            $html = $this->template->render($view, $args);

            return new Response(200, [], $html);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            $this->logger->error($e->getMessage());

            try {
                $html = $this->template->render('error/500.twig');
            } catch (LoaderError|RuntimeError|SyntaxError $e) {
                $this->logger->error($e->getMessage());
                $html = "500 Internal Server Error";
            }

            return new Response(500, [], $html);
        }
    }
}
