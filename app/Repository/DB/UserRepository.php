<?php

declare(strict_types=1);

namespace App\Repository\DB;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository
{
    public static function createUser(array $userArray)
    {
        $user = new User();
        $user->name = $userArray['name'];
        $user->email = $userArray['email'];
        $user->password = $userArray['password'];
        $user->admin = false;

        throw_if(!$user->save(), new DBException("error creating User entry on DB"));
        
        return $user;
    }

    public static function createAdminUser(array $userArray)
    {
        $user = new User();
        $user->name = $userArray['name'];
        $user->email = $userArray['email'];
        $user->password = $userArray['password'];
        $user->admin = true;
        var_dump($userArray);
        throw_if(!$user->save(), new DBException("error creating Admin User entry on DB"));

        return $user;
    }
}
