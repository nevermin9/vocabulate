<?php
declare(strict_types=1);

namespace App\Core;

/**
* @property-read ?array db
* @property-read ?array mail
*/
final class Config
{
    private array $config;

    public function __construct(array $env)
    {
        $this->config = [
            'db' => [
                'host' => $env['DB_HOST'],
                'driver' => $env['DB_DRIVER'] ?? 'mysql',
                'name' => $env['DB_NAME'],
                'user' => $env['DB_USER'],
                'password' => $env['DB_PASSWORD'],
            ],
            'mail' => [
                'sender_email' => $env['SENDER_EMAIL'],
                'sender_name' => $env['SENDER_NAME'],
            ]
        ];
    }

    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }
}
