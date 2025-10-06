<?php
declare(strict_types=1);

namespace App\DTO\Mails;

class VerificationMail extends AbstractMail
{
    public function __construct(
        protected string $subject,
        protected string $body = ''
    )
    {
        parent::__construct(Mail::Html, $subject, $body);
    }

    protected static function getTemplatePath(): string
    {
        return parent::getTemplatePath() . "verification-link.html";
    }
}
