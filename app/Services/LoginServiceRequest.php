<?php

namespace App\Services;

class LoginServiceRequest
{
    private string $name;
    private string $password;
    private bool $remember;

    public function __construct(string $name, string $password, bool $remember)
    {
        $this->name = $name;
        $this->password = $password;
        $this->remember = $remember;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isRemember(): bool
    {
        return $this->remember;
    }

}
