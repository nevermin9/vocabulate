<?php
declare(strict_types=1);

namespace App\Forms;

use App\Forms\Enums\Rule;

final class ForgotPasswordForm extends AbstractForm
{
    public readonly string $email;

    public function rules(): array
    {
        return [
            "email" => [Rule::Email]
        ];
    }
}
