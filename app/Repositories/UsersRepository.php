<?php

namespace App\Repositories;

use App\Models\User;

interface UsersRepository
{
    public function save(User $user): void;
}
