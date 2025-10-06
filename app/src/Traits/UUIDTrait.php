<?php
declare(strict_types=1);

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UUIDTrait {
    private static function generateIdBytes(): string
    {
        return Uuid::uuid7()->getBytes();
    }

    private static function convertBytesToString(string $idBytes): string
    {
        return Uuid::fromBytes($idBytes)->toString();
    }

    private static function convertStringToBytes(string $idString): string
    {
        return Uuid::fromString($idString)->getBytes();
    }
}
