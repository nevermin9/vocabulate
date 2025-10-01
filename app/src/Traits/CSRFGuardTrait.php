<?php
declare(strict_types=1);

namespace App\Traits;

trait CSRFGuardTrait
{
    public function forbidAndExit(string $redirectPath)
    {
        session_regenerate_id(true);
        redirect($redirectPath, 403);
        die();
    }
}
