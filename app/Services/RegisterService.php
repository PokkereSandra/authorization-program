<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UsersRepository;

class RegisterService
{
    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function execute(RegisterServiceRequest $request): User
    {

        $user = new User(
            $request->getName(),
            $request->getEmail(),
            $request->getPassword(),
            $request->getAgreement(),
        );

        $this->usersRepository->save($user);

        return $user;
    }

    public function findUserByName(string $name): string
    {
        return $this->usersRepository->checkNameInDb($name);
    }

    public function findUserByEmail(string $email): string
    {
        return $this->usersRepository->checkEmailInDb($email);
    }


    public function edit(RegisterServiceRequest $request): void
    {
        $user = new User(
            $request->getName(),
            $request->getEmail(),
            $request->getPassword(),
            $request->getAgreement(),
            $_SESSION['auth_id'],
        );
        $this->usersRepository->changeUserData($user);
    }

}
