<?php
declare(strict_types=1);

namespace App\Database\Enums;

enum Order: string
{
    case Asc = 'ASC';
    case Desc = 'DESC';
}
