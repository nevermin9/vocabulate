<?php
declare(strict_types=1);

namespace App\DTO\Mails;

interface AddresseeInterface
{
    public string $address { get; }
    public string $name { get; }
}
