<?php

namespace App\Services;

class RegisterServiceRequest
{
    private string $name;
    private string $email;
    private string $password;
    private bool $agreement = false;

    public function __construct(string $name, string $email, string $password, string $agreement)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        if ($agreement === 'on') {
            $this->agreement = true;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAgreement(): bool
    {
        return $this->agreement;
    }

}
