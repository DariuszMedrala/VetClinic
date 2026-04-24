<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

final class AuthMiddleware implements Middleware
{
    private Auth $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    public function handle(Request $request): ?Response
    {
        if ($this->auth->check()) {
            return null;
        }

        if ($request->wantsJson()) {
            return (new Response())->status(401)->json(['error' => 'Wymagane uwierzytelnienie.']);
        }

        return (new Response())->redirect('/login');
    }
}
