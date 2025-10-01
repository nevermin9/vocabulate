<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class UserService
{
    public function register(string $email, string $password): User
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $username = "user" . mt_rand(100, 999);

        $user = new User($username, $email, $passwordHash);

        $user->save();

        return $user;
    }
}
