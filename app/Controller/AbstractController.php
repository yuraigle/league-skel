<?php

namespace App\Controller;

use Ahc\Jwt\JWTException;
use App\Service\AuthService;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment as TemplateEngine;
use Twig\Error\Error as TwigError;

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

    public function getAuth(): ?array
    {
        $token = $this->request->getCookieParams()['auth'] ?? null;

        if ($token) {
            try {
                return AuthService::parseJwt($token);
            } catch (JWTException) {
            }
        }

        return null;
    }

    protected function render($view, $args = []): Psr7Response
    {
        try {
            $html = $this->template->render($view, $args);

            return new Response(200, [], $html);
        } catch (TwigError $e) {
            $this->logger->error($e->getMessage());

            try {
                $html = $this->template->render('error/500.twig');
            } catch (TwigError) {
                $html = "500 Server Error";
            }

            return new Response(500, [], $html);
        }
    }

    protected function redirect(string $url, int $code = 302): Psr7Response
    {
        return new Response($code, ['Location' => $url]);
    }

    protected function json($data): Psr7Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($data));
    }

    protected function cookie(string $name, string $value, int $maxAge = null): string
    {
        if ($maxAge === null) {
            $maxAge = $_ENV['COOKIE_LIFETIME'];
        }

        $expires = gmdate("D, d M Y H:i:s \G\M\T", time() + $maxAge);

        return sprintf(
            '%s=%s; path=/; Secure; HttpOnly; SameSite=strict; Expires=%s',
            $name,
            $value,
            $expires
        );
    }
}
