<?php

namespace App\Repository;

use App\Repository\Contracts\UserRepository as UserRepositoryContract;
use App\User;

class UserRepository implements UserRepositoryContract
{
    public function getById(int $id) : ?User
    {
        return User::find($id);
    }
}
