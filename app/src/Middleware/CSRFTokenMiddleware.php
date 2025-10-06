<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Request;
use App\Traits\CSRFGuardTrait;

class CSRFTokenMiddleware implements MiddlewareInterface
{
    use CSRFGuardTrait;

    public function handle(Request $req): mixed
    {
        if ($req->method !== "post") {
            return null;
        }

        $auth = Application::authService();

        if (empty($req->data['csrf_token']) || ! $auth->checkCSRF($req->data['csrf_token'])) {
            $this->forbidAndExit();
        } 

        return null;
    }
}
