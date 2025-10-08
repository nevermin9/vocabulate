<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Enums\HttpMethod;

class Request
{
    public function __construct(
        public readonly string $path,
        public readonly HttpMethod $method,
        public readonly string $uri,
        public readonly array $data,
        public readonly string $host, 
        public readonly array $headers,
        public readonly array $cookies,
    )
    {
    }
}

