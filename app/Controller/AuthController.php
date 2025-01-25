<?php

namespace App\Controller;

use App\Service\AuthService;
use Exception;
use League\Route\Http\Exception\BadRequestException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface as Psr7Response;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function login(): Psr7Response
    {
        return $this->render("auth/login.twig");
    }

    public function loginPost(): Psr7Response
    {
        $username = $this->getRequest()->getParsedBody()['username'] ?? null;
        $password = $this->getRequest()->getParsedBody()['password'] ?? null;

        try {
            if ($username === null || $password === null) {
                throw new BadRequestException('Form data is invalid');
            }

            $auth = $this->authService->authenticate($username, $password);

            $cookieVal = base64_encode(json_encode($auth));
            $cookieSign = hash_hmac('sha256', $cookieVal, $_ENV['COOKIE_SECRET']);

            return (new Response(303, ['Location' => '/']))
                ->withAddedHeader('Set-Cookie', sprintf('auth=%s; path=/; HttpOnly', $cookieVal))
                ->withAddedHeader('Set-Cookie', sprintf('auth_sign=%s; path=/; HttpOnly', $cookieSign));
        } catch (Exception $e) {
            return $this->render("auth/login.twig", [
                "error" => $e->getMessage(),
                "form" => $this->getRequest()->getParsedBody(),
            ]);
        }
    }

    public function logout(): Psr7Response
    {
        return (new Response(303, ['Location' => '/']))
            ->withAddedHeader('Set-Cookie', 'auth=; path=/; HttpOnly')
            ->withAddedHeader('Set-Cookie', 'auth_sign=; path=/; HttpOnly');
    }
}