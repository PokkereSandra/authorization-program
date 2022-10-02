<?php

namespace App\Services;

use App\Auth;
use App\Cookie;
use App\Repositories\MySQLUsersRepository;

class LoginService
{
    private MySQLUsersRepository $usersRepository;

    public function __construct(MySQLUsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function execute(LoginServiceRequest $request): void
    {
        {
            $user = $this->usersRepository->getByName($request->getName());

            if ($user) {
                if (password_verify($request->getPassword(), $user->getPassword())) {
                    Auth::authorize($user->getId());
                    if ($request->isRemember()) {
                        $hash = hash('sha256', uniqid($user->getId()));
                        $hashCheck = $this->usersRepository->checkTokenById($user->getId());
                        if (empty($hashCheck)) {
                            $this->usersRepository->saveToken($user->getId(), $hash);
                        } else {
                            $hash = $hashCheck['token'];
                        }
                        Cookie::put('remember_me', $hash, 604800);
                    }
                }
            } else {
                $_SESSION['errors'] = 'Invalid username or password';
            }
        }
    }
}
