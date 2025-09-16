<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class UserService
{
    public function register(string $password, string $email): User
    {
        // password should be validated beforehand
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (! $email) {
            throw new \Exception("email is not valid");
        }

        $username = "user" . mt_rand(100, 999);

        $user = new User($username, $email, $passwordHash);

        $user->create();

        return $user;
    }

    public function verifyUser(string $password, string $email): ?User
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (! $email) {
            throw new \Exception("email is not valid");
        }

        $user = User::get($email);

        if (password_verify($password, $user->passwordHash)) {
            return $user;
        }

        return null;
    }
}
