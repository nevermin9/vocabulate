<?php
declare(strict_types=1);

namespace App\Forms;

use App\Forms\Enums\Rule;
use App\Models\User;

final class LoginForm extends AbstractForm
{
    const INVALID_CRED_ERROR = 'Invalid credentials';

    public readonly string $email;
    public readonly string $password;

    public function rules(): array
    {
        return [
            'email' => [Rule::Email]
        ];
    }

    public function validateUserPassword(?User $user): bool
    {
        if (! $user || ! password_verify($this->password, $user->getPasswordHash())) {
            $this->addError('credentials', static::INVALID_CRED_ERROR);
            return false;
        }

        return true;
    }
}
