<?php

namespace App\Controller;

use App\Service\AuthService;
use Exception;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

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
        $form = $this->getRequest()->getParsedBody();

        // just an example of form validation
        $formValidator = v::arrayType()
            ->key('username', v::stringType()->notBlank()->length(5, 20)->regex('/^[a-zA-Z0-9@_.]{5,20}$/'))
            ->key('password', v::stringType()->notBlank()->length(8, 20));

        try {
            $formValidator->assert($form);

            $auth = $this->authService->authenticate($form['username'], $form['password']);

            $cookieVal = base64_encode(json_encode($auth));
            $cookieSign = hash_hmac('sha256', $cookieVal, $_ENV['COOKIE_SECRET']);

            return (new Response(303, ['Location' => '/']))
                ->withAddedHeader('Set-Cookie', sprintf('auth=%s; path=/; HttpOnly', $cookieVal))
                ->withAddedHeader('Set-Cookie', sprintf('auth_sign=%s; path=/; HttpOnly', $cookieSign));
        } catch (NestedValidationException $e) {
            return $this->render("auth/login.twig", [
                'messages' => $e->getMessages(),
                'form' => $form,
            ]);
        } catch (Exception $e) {
            return $this->render("auth/login.twig", [
                "messages" => [$e->getMessage()],
                "form" => $form,
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
