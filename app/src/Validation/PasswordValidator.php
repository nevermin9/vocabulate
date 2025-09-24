<?php
declare(strict_types=1);

namespace App\Validation;

final class PasswordValidator
{

    private static $validatorsMap = null;

    private static function getValidatorsMap(): array
    {
        if (static::$validatorsMap === null) {
            static::$validatorsMap = [
                'length' => [static fn($pass) => strlen($pass) < 8, "Password must be at least 8 characters long."],
                'uppercase' => [static fn($pass) => ! preg_match('/[A-Z]/', $pass), "Password must contain at least one uppercase letter."],
                'lowercase' => [static fn($pass) => ! preg_match('/[a-z]/', $pass), "Password must contain at least one lowercase letter."],
                'number' => [static fn($pass) => ! preg_match('/[0-9]/', $pass), "Password must contain at least one number."],
                'special' => [static fn($pass) => ! preg_match('/[^A-Za-z0-9]/', $pass), "Password must contain at least one special character."]
            ];
        }

        return self::$validatorsMap;
    }

    /**
     * Returns an array of validation rules with their error messages.
     *
     * @return array
     */
    public static function getRules(): array
    {
        $rules = [];
        foreach (static::getValidatorsMap() as $id => $rule) {
            $rules[$id] = $rule[1];
        }
        return $rules;
    }

    public static function validate(string $password): array
    {
        $errors = [];
        foreach (static::getValidatorsMap() as $id => $rule) {
            if ($rule[0]($password)) {
                $errors[$id] = $rule[1];
            }
        }
        return $errors;
    }

}
