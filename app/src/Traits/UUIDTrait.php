<?php
declare(strict_types=1);

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UUIDTrait {
    private function generateIdBytes(): string
    {
        return Uuid::uuid7()->getBytes();
    }

    private function convertBytesToString(string $idBytes): string
    {
        return Uuid::fromBytes($idBytes)->toString();
    }

    private function convertStringToBytes(string $idString): string
    {
        return Uuid::fromString($idString)->getBytes();
    }
}
