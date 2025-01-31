<?php

namespace App\Controller;

use App\Service\AuthService;
use Exception;
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
        $form = json_decode($this->getRequest()->getBody(), true);

        // a backend validation example
        $formValidator = v::arrayType()
            ->key('username', v::stringType()->notBlank()->alnum()->length(5, 20))
            ->key('password', v::stringType()->notBlank()->length(5, 20));

        try {
            $formValidator->assert($form);
            $auth = $this->authService->authenticate($form['username'], $form['password']);
            $jwt = AuthService::generateJwt($auth);

            return $this->json(['status' => 'success', 'redirect' => '/'])
                ->withAddedHeader('Set-Cookie', $this->cookie('auth', $jwt));
        } catch (NestedValidationException $e) {
            $msg = array_values($e->getMessages())[0] ?? 'Invalid form data.';
            return $this->json(['status' => 'error', 'message' => $msg])->withStatus(400);
        } catch (Exception $e) {
            $msg = $e->getMessage() ?? 'Authentication failed.';
            return $this->json(['status' => 'error', 'message' => $msg])->withStatus(401);
        }
    }

    public function logout(): Psr7Response
    {
        return $this->redirect('/', 303)
            ->withAddedHeader('Set-Cookie', $this->cookie('auth', '', -1));
    }
}
