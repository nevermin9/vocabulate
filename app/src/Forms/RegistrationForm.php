<?php
declare(strict_types=1);

namespace App\Forms;

use App\Forms\Enums\Rule;

final class RegistrationForm extends ResetPasswordForm
{
    public readonly string $email;

    public function rules(): array
    {
        return [
            'email' => [Rule::Email],
            'password' => [[Rule::Min, Rule::Min->value => 8 ], Rule::AUppercase, Rule::ALowercase, Rule::ANumber, Rule::ASpecial],
            'confirmPassword' => [[Rule::Match, Rule::Match->value => 'password']]
        ];
    }
}
