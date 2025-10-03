<?php
declare(strict_types=1);

namespace App\Models;

class ForgotPasswordToken extends AbstractToken {

    protected const EXPIRES_IN_SECONDS = 30 * 60;

    public static function getTableName(): string
    {
        return 'forgot_pass_tokens';
    }

    public function __construct(string $userId)
    {
        parent::__construct($userId, self::EXPIRES_IN_SECONDS);
    }
}
