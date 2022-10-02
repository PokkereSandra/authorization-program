<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\RegisterService;
use App\Services\RegisterServiceRequest;
use App\Views\TwigView;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Rules;

class ProfileController
{
    private RegisterService $profile;

    public function __construct(RegisterService $profile)
    {
        $this->profile = $profile;
    }

    public function profile(): TwigView
    {
        if (!Auth::isAuthorized()) {
            $_SESSION['errors'] = 'You must be logged in to access profile';
            return new TwigView('home', ['errors' => $_SESSION['errors']]);
        }
        return new TwigView('profile', []);
    }

    public function edit(): Redirect
    {
        $name = $this->profile->findUserByName($_POST['name']);
        $email = $this->profile->findUserByEmail($_POST['email']);

        if ($name && (int)$name !== $_SESSION['auth_id']) {
            $_SESSION['errors']['name'] = 'user exists';
            return new Redirect('/profile');
        }

        if ($email && (int)$email !== $_SESSION['auth_id']) {
            $_SESSION['errors']['email'] = 'email exists';
            return new Redirect('/profile');
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
        );

        try {
            $validator->assert($_POST);
            $this->profile->edit(new RegisterServiceRequest(
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
                'on',
            ));
            $_SESSION['success'] = 'data changed successfully';
            return new Redirect('/profile');

        } catch (NestedValidationException $exception) {

            $messages = $exception->getMessages();
            if (isset($messages['re_pass'])) {
                $messages['re_pass'] = 'passwords must be identical';
            }
            if (!empty($_SESSION['errors'])) {
                $_SESSION['errors'] = array_merge($_SESSION['errors'], $messages);
            } else {
                $_SESSION['errors'] = $messages;
            }
            return new Redirect('/profile');
        }

    }
}
