<?php
declare(strict_types=1);

namespace App\Core\Enums;

enum HttpStatus: int
{
    // 2xx Success
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;
    
    // 3xx Redirection
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENT_REDIRECT = 308;
    
    // 4xx Client Errors
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case UNPROCESSABLE_ENTITY = 422;
    case TOO_MANY_REQUESTS = 429;
    
    // 5xx Server Errors
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;

    /**
     * Get the human-readable reason phrase for the status code.
     */
    public function reasonPhrase(): string
    {
        return match($this) {
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NO_CONTENT => 'No Content',
            
            self::MOVED_PERMANENTLY => 'Moved Permanently',
            self::FOUND => 'Found',
            self::SEE_OTHER => 'See Other',
            self::NOT_MODIFIED => 'Not Modified',
            self::TEMPORARY_REDIRECT => 'Temporary Redirect',
            self::PERMANENT_REDIRECT => 'Permanent Redirect',
            
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            self::NOT_ACCEPTABLE => 'Not Acceptable',
            self::REQUEST_TIMEOUT => 'Request Timeout',
            self::CONFLICT => 'Conflict',
            self::GONE => 'Gone',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::TOO_MANY_REQUESTS => 'Too Many Requests',
            
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::NOT_IMPLEMENTED => 'Not Implemented',
            self::BAD_GATEWAY => 'Bad Gateway',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable',
            self::GATEWAY_TIMEOUT => 'Gateway Timeout',
        };
    }
    
    /**
     * Check if the status code indicates success (2xx).
     */
    public function isSuccess(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }
    
    /**
     * Check if the status code indicates a client error (4xx).
     */
    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }
    
    /**
     * Check if the status code indicates a server error (5xx).
     */
    public function isServerError(): bool
    {
        return $this->value >= 500 && $this->value < 600;
    }
    
    /**
     * Check if the status code is an error (4xx or 5xx).
     */
    public function isError(): bool
    {
        return $this->isClientError() || $this->isServerError();
    }
}
