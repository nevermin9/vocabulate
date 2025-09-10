<?php
declare(strict_types=1);

namespace App\Services;

final class Request
{
    public string $method;
    public array $data;
    public string $uri;
    public string $host; 
    public array $headers;
    public array $cookies;

    public function __construct()
    {
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->headers = static::getAllHeaders();
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->cookies = $_COOKIE;

        if ($this->method === "post" && $this->headers['Content-Type'] === 'application/json') {
            $json = file_get_contents('php://input');
            $this->data = (array) json_decode($json);
        } elseif ($this->method === "post") {
            $this->data = $_POST;
        } else {
            $this->data = $_GET;
        }
    }

    private static function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) !== 'HTTP_') {
                continue;
            }

            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', substr($name, 5))));
            $headers[$header] = $value;
        }

        return $headers;

    }
}
