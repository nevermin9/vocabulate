<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Request;
use App\Services\AuthService;

class VerificationMiddleware implements MiddlewareInterface
{
    public function __construct(protected AuthService $auth)
    {
    }

    public static function getVerificationURL(): string
    {
        return "/verification-pending";
    }

    public function handle(Request $req): mixed
    {
        $isVerified = $this->auth->getUser()?->isVerified();

        if ($isVerified) {
            return null;
        }

        redirect(static::getVerificationURL());
        die();
    }
}
