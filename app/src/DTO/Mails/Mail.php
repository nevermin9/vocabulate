<?php
declare(strict_types=1);

namespace App\DTO\Mails;

enum Mail: string
{
    case Html = 'html';
    case Plain = 'plain';
    // case Calendar = 'calendar';
}
