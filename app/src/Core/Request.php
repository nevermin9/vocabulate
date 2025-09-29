<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    public readonly string $method;
    public readonly array $data;
    public readonly string $uri;
    public readonly string $host; 
    public readonly array $headers;
    public readonly array $cookies;
    public readonly array $params;

    public function __construct()
    {
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->headers = static::getAllHeaders();
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->cookies = $this->sanitize($_COOKIE, INPUT_COOKIE);

        if ($this->method === "post" && isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
            $json = file_get_contents('php://input');
            $this->data = (array) json_decode($json);
        } elseif ($this->method === "post") {
            $this->data = $this->sanitize($_POST, INPUT_POST);
        } else {
            $this->data = $this->sanitize($_GET, INPUT_GET);
        }
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

    public function setParams(array $params)
    {
        if (! $this->params) {
            $this->params = $params;
        }
    }

    private function sanitize(array $data, int $type): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[] = filter_input($type, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $sanitized;
    }
}


