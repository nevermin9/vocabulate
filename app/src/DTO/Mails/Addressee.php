<?php
declare(strict_types=1);

namespace App\DTO\Mails;

class Addressee implements AddresseeInterface
{
    public function __construct(
        public readonly string $address,
        public readonly string $name
    )
    {
    }
}
