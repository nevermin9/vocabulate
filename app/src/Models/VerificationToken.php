<?php
declare(strict_types=1);

namespace App\Models;

class VerificationToken extends AbstractToken {

    public function __construct(string $userId)
    {
        parent::__construct($userId, 'verification_tokens');
    }
}
