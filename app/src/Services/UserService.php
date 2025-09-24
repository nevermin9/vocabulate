<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Validation\PasswordValidator;

final class UserService
{
    public static function getPasswordRules(): array
    {
        return PasswordValidator::getRules();
    }

    public function register(string $password, string $confirmPassword, string $email): array
    {
        if ($password !== $confirmPassword) {
            $errors['confirm_password'][] = "Passwords should match";
        }

        $passwordErrors = PasswordValidator::validate($password);

        if (! empty($passwordErrors)) {
            $errors['password'] = $passwordErrors;
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (! $email) {
            $errors['email'] = "Email is not valid.";
        }

        if (! empty($errors)) {
            return [null, $errors];
        }

        // if email exists, finish registration and send a link to the user
        // check by retrieving user by email OR by error from User

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $username = "user" . mt_rand(100, 999);

        $user = new User($username, $email, $passwordHash);

        $user->create();

        return [$user, null];
    }

    public function verifyUser(string $password, string $email): array
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (! $email) {
            throw new \Exception("email is not valid");
        }

        $user = User::get($email);

        if ($user) {
            if (password_verify($password, $user->passwordHash)) {
                return [$user, null];
            }
        }

        $errors = ["email" => "Invalid credentials"];

        return [null, $errors];
    }
}
