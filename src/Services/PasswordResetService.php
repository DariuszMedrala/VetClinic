<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use DateTimeImmutable;

final class PasswordResetService
{
    private const TTL_MINUTES = 60;

    private UserRepository $users;
    private PasswordResetRepository $resets;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->resets = new PasswordResetRepository();
    }

    public function request(string $email): void
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expires = (new DateTimeImmutable('+' . self::TTL_MINUTES . ' minutes'))->format('Y-m-d H:i:s');

        $this->resets->create($user->id, hash('sha256', $token), $expires);

        error_log('[VetClinic] Link do resetu hasła dla ' . $email . ': /reset-hasla/' . $token);
    }

    public function tokenValid(string $token): bool
    {
        return $this->resets->findUserByToken(hash('sha256', $token)) !== null;
    }

    public function reset(string $token, string $password): bool
    {
        $userId = $this->resets->findUserByToken(hash('sha256', $token));

        if ($userId === null) {
            return false;
        }

        $this->users->updatePassword($userId, password_hash($password, PASSWORD_BCRYPT));
        $this->resets->deleteForUser($userId);

        return true;
    }
}
