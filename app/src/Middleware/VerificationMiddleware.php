<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Request;

class VerificationMiddleware implements MiddlewareInterface
{
    public static function getVerificationURL(): string
    {
        return "/verification-pending";
    }

    public function handle(Request $req): mixed
    {
        $auth = Application::authService();
        $isVerified = $auth->getUser()->isVerified();

        if ($isVerified) {
            return null;
        }

        redirect(static::getVerificationURL());
        die();
    }
}
