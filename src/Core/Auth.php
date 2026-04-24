<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private Session $session;

    public function __construct(?Session $session = null)
    {
        $this->session = $session ?? new Session();
        $this->session->start();
    }

    public function check(): bool
    {
        return $this->session->has('user');
    }

    public function user(): ?array
    {
        return $this->session->get('user');
    }

    public function id(): ?int
    {
        return $this->user()['id'] ?? null;
    }

    public function role(): ?string
    {
        return $this->user()['role'] ?? null;
    }

    public function hasRole(string ...$roles): bool
    {
        $role = $this->role();

        return $role !== null && in_array($role, $roles, true);
    }
}
