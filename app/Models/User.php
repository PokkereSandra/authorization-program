<?php

namespace App\Models;

class User
{
    private string $name;
    private string $email;
    private string $password;
    private ?int $id;
    private string $agreement;

    public function __construct(string $name, string $email, string $password, string $agreement, ?int $id = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
        $this->agreement = $agreement;
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

    public function getAgreement(): string
    {
        return $this->agreement;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}
