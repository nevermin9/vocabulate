<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Core\Enums\HttpStatus;

class NotFoundException extends HttpException
{
    public function __construct(
        string $message = 'Resource Not Found',
        int $code = 0,
        ?\Throwable $previous = null
    )
    {
        parent::__construct($message, HttpStatus::NOT_FOUND, $code, $previous);
    }

    /**
     * Named constructor for route not found errors.
     */
    public static function forRoute(string $path, string $method): static
    {
        $method = strtoupper($method);
        return new static("Route not found: {$method} {$path}");
    }

    /**
     * Named constructor for resource not found errors.
     */
    public static function forResource(string $resource, string $id): static
    {
        return new static("Resource '{$resource}' with ID '{$id}' not found");
    }

}
