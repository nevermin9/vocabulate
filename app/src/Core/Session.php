<?php
declare(strict_types=1);

namespace App\Core;

use App\Attributes\Container\Singleton;

#[Singleton]
class Session
{
    protected const FLASH_DATA_KEY = 'flash-data';

    public function __construct()
    {
        session_start();
        $flashData = $_SESSION[static::FLASH_DATA_KEY] ?? [];
        foreach ($flashData as $key => &$data) {
            $data['remove'] = true;
        }
        $_SESSION[static::FLASH_DATA_KEY] = $flashData;
    }

    public function setFlash(string $key, mixed $data): void
    {
        $_SESSION[static::FLASH_DATA_KEY][$key] = [
            'remove' => false,
            'data' => $data,
        ];
    }

    public function getFlash(string $key): mixed
    {
        return $_SESSION[static::FLASH_DATA_KEY][$key]['data'] ?? null;
    }

    public function set(string $key, mixed $data): void
    {
        $_SESSION[$key] = $data;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->removeAllFlashData();
    }

    public function restart(): void
    {
        session_regenerate_id(true);
    }

    public function clear(): void
    {
        session_unset();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
        session_destroy();
    }

    protected function removeAllFlashData(): void
    {
        $flashData = $_SESSION[static::FLASH_DATA_KEY] ?? [];
        foreach ($flashData as $key => $data) {
            if ($data['remove']) {
                unset($flashData[$key]);
            }
        }
        $_SESSION[static::FLASH_DATA_KEY] = $flashData;
    }
}
