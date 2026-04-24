<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

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

        if ($request->wantsJson()) {
            return (new Response())->status(403)->json(['error' => 'Brak uprawnień.']);
        }

        $view = new View(APP_ROOT . '/src/Views');

        return (new Response())
            ->status(403)
            ->html($view->render('errors/403', ['title' => 'VetClinic — Brak dostępu'], 'base'));
    }
}
