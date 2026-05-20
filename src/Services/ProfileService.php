<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;

final class ProfileService
{
    private UserRepository $users;
    private ClientRepository $clients;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->clients = new ClientRepository();
    }

    public function get(int $userId): ?Client
    {
        return $this->clients->find($userId);
    }

    public function updateData(int $userId, string $firstName, string $lastName, string $email, ?string $phone): array
    {
        if ($this->users->emailExistsForOther($email, $userId)) {
            return ['ok' => false, 'message' => 'Ten adres e-mail jest już zajęty.'];
        }

        $this->users->updateProfile($userId, $firstName, $lastName, $email);
        $this->clients->updatePhone($userId, $phone);

        return ['ok' => true, 'message' => 'Dane zostały zapisane.'];
    }

    public function changePassword(int $userId, string $current, string $new): array
    {
        $user = $this->users->findById($userId);

        if ($user === null || !password_verify($current, $user->passwordHash)) {
            return ['ok' => false, 'message' => 'Aktualne hasło jest nieprawidłowe.'];
        }

        $this->users->updatePassword($userId, password_hash($new, PASSWORD_BCRYPT));

        return ['ok' => true, 'message' => 'Hasło zostało zmienione.'];
    }
}
