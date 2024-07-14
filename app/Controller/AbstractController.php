<?php

namespace App\Controller;

use Laminas\Diactoros\Response\HtmlResponse;
use League\Plates\Engine as TemplateEngine;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractController implements LoggerAwareInterface
{
    protected TemplateEngine $template;
    protected LoggerInterface $logger;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setTemplate(TemplateEngine $template): void
    {
        $this->template = $template;
    }

    protected function render($view, $args = []): Psr7Response
    {
        $html = $this->template->render($view, $args);

        return new HtmlResponse($html);
    }
}
