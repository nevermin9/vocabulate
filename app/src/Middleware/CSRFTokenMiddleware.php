<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Request;
use App\Services\AuthService;
use App\Traits\CSRFGuardTrait;

class CSRFTokenMiddleware implements MiddlewareInterface
{
    use CSRFGuardTrait;

    public function __construct(protected AuthService $auth)
    {
    }

    public function handle(Request $req): mixed
    {
        if (! $req->method->isPost()) {
            return null;
        }

        if (empty($req->data['csrf_token']) || ! $this->auth->checkCSRF($req->data['csrf_token'])) {
            $this->forbidAndExit();
        } 

        return null;
    }
}
