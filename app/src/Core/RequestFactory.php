<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Request;

class RequestFactory
{
    public static function createFromGlobals(): Request
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $headers = static::getAllHeaders();
        $uri = $_SERVER['REQUEST_URI'];
        $cookies = static::sanitize($_COOKIE, INPUT_COOKIE);
        $host = $_SERVER['HTTP_HOST'] ?? 'unknown';
        $path = explode("?", $uri)[0];

        if ($method === "post" && isset($headers['Content-Type']) && $headers['Content-Type'] === 'application/json') {
            $json = file_get_contents('php://input');
            $data = (array) json_decode($json);
        } elseif ($method === "post") {
            $data = static::sanitize($_POST, INPUT_POST);
        } else {
            $data = static::sanitize($_GET, INPUT_GET);
        }

        return static::create(
            path: $path,
            method: $method,
            uri: $uri,
            data: $data,
            host: $host,
            headers: $headers,
            cookies: $cookies,
        );
    }

    public static function create(
        string $path = '/',
        string $method = 'get',
        string $uri = '/',
        array $data = [],
        string $host = 'localhost', 
        array $headers = [],
        array $cookies = [],
    ): Request
    {
        return new Request(
            path: $path,
            method: $method,
            uri: $uri,
            data: $data,
            host: $host,
            headers: $headers,
            cookies: $cookies,
        );
    }

    private static function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) !== 'HTTP_') {
                continue;
            }

            $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
            $headers[$header] = $value;
        }

        return $headers;
    }

    private static function sanitize(array $data, int $type): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = filter_input($type, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $sanitized;
    }
}
