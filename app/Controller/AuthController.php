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
        $form = $this->getRequest()->getParsedBody();

        // just an example of form validation
        $formValidator = v::arrayType()
            ->key('username', v::stringType()->notBlank()->length(5, 20))
            ->key('password', v::stringType()->notBlank()->length(5, 20));

        try {
            $formValidator->assert($form);
            $auth = $this->authService->authenticate($form['username'], $form['password']);
            $jwt = AuthService::generateJwt($auth);

            return $this->redirect('/', 303)
                ->withAddedHeader('Set-Cookie', $this->cookie('auth', $jwt));
        } catch (NestedValidationException $e) {
            return $this->render("auth/login.twig", [
                "messages" => $e->getMessages(),
                "form" => $form,
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
        return $this->redirect('/', 303)
            ->withAddedHeader('Set-Cookie', $this->cookie('auth', '', -1));
    }
}
