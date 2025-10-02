<?php
declare(strict_types=1);

namespace App\DTO\Mails;

class ResetPasswordMail extends AbstractMail
{
    protected static function getTemplatePath(): string
    {
        return dirname(__DIR__) . "/../MailTemplates/reset-password.html";
    }
}
