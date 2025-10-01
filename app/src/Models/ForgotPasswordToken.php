<?php
declare(strict_types=1);

namespace App\Models;

class ForgotPasswordToken extends AbstractToken {

    public function __construct(string $userId)
    {
        parent::__construct($userId, 'forgot_pass_tokens');
    }
}
