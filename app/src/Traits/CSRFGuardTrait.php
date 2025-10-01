<?php
declare(strict_types=1);

namespace App\Traits;

trait CSRFGuardTrait
{
    public function forbidAndExit()
    {
        session_regenerate_id(true);
        http_response_code(403);
        die("Your request could not be processed due to a security check. Please try submitting the form again.");
    }
}
