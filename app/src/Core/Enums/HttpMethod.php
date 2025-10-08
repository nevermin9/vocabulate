<?php

namespace App\Core\Enums;

enum HttpMethod: string
{
    case GET     = 'GET';
    case POST    = 'POST';
    case PUT     = 'PUT';
    case PATCH   = 'PATCH';
    case DELETE  = 'DELETE';
    case HEAD    = 'HEAD';
    case OPTIONS = 'OPTIONS';
    case CONNECT = 'CONNECT';
    case TRACE   = 'TRACE';

    /**
     * Returns an array of HTTP methods that typically contain a request body.
     * @return HttpMethod[]
     */
    public static function withBody(): array
    {
        return [
            self::POST,
            self::PUT,
            self::PATCH,
            // DELETE
        ];
    }

    /**
     * @return string[]
     */
    public static function allValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isGet(): bool
    {
        return $this === self::GET;
    }

    public function isPost(): bool
    {
        return $this === self::POST;
    }

    public function isPut(): bool
    {
        return $this === self::PUT;
    }

    public function isPatch(): bool
    {
        return $this === self::PATCH;
    }

    public function isDelete(): bool
    {
        return $this === self::DELETE;
    }
    
    public function isHead(): bool
    {
        return $this === self::HEAD;
    }

    public function isOptions(): bool
    {
        return $this === self::OPTIONS;
    }

    public function hasBody(): bool
    {
        return in_array($this, self::withBody(), true);
    }
}
