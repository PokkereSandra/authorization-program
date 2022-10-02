<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\RegisterService;
use App\Services\RegisterServiceRequest;
use App\Views\TwigView;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Rules;

class RegisterController
{
    private RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function store(): Redirect
    {
        if (!isset($_POST['agree_term'])) {
            $_POST['agree_term'] = 'off';
        }
        $name = $this->registerService->findUserByName($_POST['name']);
        $email = $this->registerService->findUserByEmail($_POST['email']);

        if (!empty($name)) {
            $_SESSION['errors']['name'] = 'user exists';
        }

        if (!empty($email)) {
            $_SESSION['errors']['email'] = 'email exists';
        }

        $validator = new Rules\KeySet(
            (new Rules\Key('name', new Rules\AllOf(
                new Rules\Alpha(),
                new Rules\NotEmpty(),
            ))),
            (new Rules\Key('email', new Rules\AllOf(
                new Rules\Email(),
                new Rules\NotEmpty(),
            ))),
            (new Rules\Key('password', new Rules\AllOf(
                new Rules\NoWhitespace(),
                new Rules\Length(8, 20),
                new Rules\NotEmpty(),
            ))),
            (new Rules\Key('re_pass', new Rules\AllOf(
                new Rules\NotEmpty(),
                new Rules\Identical($_POST['password']),
            ))),
            (new Rules\Key('agree_term', new Rules\AllOf(
                new Rules\Contains('on'),
            ))),
        );

        try {
            $validator->assert($_POST);

            $this->registerService->execute(new RegisterServiceRequest(
                    $_POST['name'],
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['agree_term'],
                )
            );

            return new Redirect('/');

        } catch (NestedValidationException $exception) {

            $messages = $exception->getMessages();
            if (isset($messages['re_pass'])) {
                $messages['re_pass'] = 'passwords must be identical';
            }
            if (isset($messages['agree_term'])) {
                $messages['agree_term'] = 'you must accept all statements in Terms of service';
            }
            if (!empty($_SESSION['errors'])) {
                $_SESSION['errors'] = array_merge($_SESSION['errors'], $messages);
            } else {
                $_SESSION['errors'] = $messages;
            }

            return new Redirect('/register');
        }
    }

    public function showRegisterForm(): TwigView
    {
        return new TwigView('registration', []);
    }
}
