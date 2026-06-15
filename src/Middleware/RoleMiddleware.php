<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\ErrorPage;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

final class RoleMiddleware implements Middleware
{
    private Auth $auth;
    private array $roles;

    public function __construct(string ...$roles)
    {
        $this->auth = new Auth();
        $this->roles = $roles;
    }

    public function handle(Request $request): ?Response
    {
        if ($this->auth->hasRole(...$this->roles)) {
            return null;
        }

        return ErrorPage::render(403, $request->wantsJson());
    }
}
