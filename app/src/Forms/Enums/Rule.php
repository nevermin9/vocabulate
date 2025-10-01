<?php
declare(strict_types=1);

namespace App\Forms\Enums;

enum Rule: string
{
    case Required = 'required';
    case Min = 'min';
    case Max = 'max';
    case Email = 'email';
    case Match = 'match';
    case ALowercase = 'at_least_lowercase';
    case AUppercase = 'at_least_uppercase';
    case ANumber = 'at_least_number';
    case ASpecial = 'at_least_special_char';
}
