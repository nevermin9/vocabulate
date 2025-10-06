<?php
declare(strict_types=1);

namespace App\Models;

class VerificationToken extends AbstractToken 
{

    public static function getTableName(): string
    {
        return 'verification_tokens';
    }

    public function __construct(string $userId)
    {
        parent::__construct($userId);
    }
    
}
