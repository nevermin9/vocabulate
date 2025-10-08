<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Enums\HttpMethod;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * @param HttpMethod $method The HTTP method.
     * @param string $path The route path.
     * @param class-string[] $middleware An array of middleware class strings.
     */
    public function __construct(
        public readonly HttpMethod $method,
        public readonly string $path,
        public readonly array $middleware = []
    )
    {
    }
}
