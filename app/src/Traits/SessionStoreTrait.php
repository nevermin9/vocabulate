<?php
declare(strict_types=1);

namespace App\Traits;

trait SessionStoreTrait
{
    public function saveInSession(string $key, mixed $data)
    {
        $_SESSION[$key] = $data;
    }

    public function getAndClearFromSession(string $key)
    {
        $data = $_SESSION[$key] ?? null;

        unset($_SESSION[$key]);

        return $data;
    }

}
