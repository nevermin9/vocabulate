<?php
declare(strict_types=1);

namespace App\DTO\Mails;

use App\DTO\Mails\Mail;

interface MailInterface
{
    public function isHTML(): bool;
    public function isPlain(): bool;
    public function getSubject(): string;
    public function getBody(): string;
}
