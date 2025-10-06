<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Core\Enums\HttpStatus;

/**
 * Base class for all HTTP-related exceptions.
 */
class HttpException extends \RuntimeException implements \Throwable
{
    protected HttpStatus $statusCode;

    public function __construct(
        string $message = '',
        HttpStatus $statusCode = HttpStatus::INTERNAL_SERVER_ERROR,
        int $code = 0,
        ?\Throwable $previous = null
    )
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
