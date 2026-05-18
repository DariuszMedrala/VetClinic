<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\User;
use App\Repositories\ClinicRepository;
use App\Repositories\UserRepository;
use Throwable;

final class AuthService
{
    private UserRepository $users;
    private ClinicRepository $clinics;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->clinics = new ClinicRepository();
    }

    public function attempt(string $email, string $password): ?User
    {
        $user = $this->users->findByEmail($email);

        if ($user !== null && password_verify($password, $user->passwordHash)) {
            return $user;
        }

        return null;
    }

    public function emailTaken(string $email): bool
    {
        return $this->users->emailExists($email);
    }

    public function register(string $firstName, string $lastName, string $email, string $password, string $formRole, ?int $clinicId, ?string $clinicName, ?string $clinicAddress): User
    {
        $role = match ($formRole) {
            'lekarz' => 'vet',
            'klient' => 'client',
            default => 'admin',
        };
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            if ($role === 'admin') {
                $clinic = $this->clinics->create((string) $clinicName, (string) $clinicAddress);
            } else {
                $clinic = (int) $clinicId;
            }

            $id = $this->users->create($email, $hash, $firstName, $lastName, $role, $clinic);

            if ($role === 'vet') {
                $license = 'LIC-' . strtoupper(bin2hex(random_bytes(3)));
                $this->users->createVetProfile($id, $license);
            }

            if ($role === 'client') {
                $this->users->createClientProfile($id, null);
            }

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        return new User($id, $email, $firstName, $lastName, $role, $hash, $clinic);
    }
}
