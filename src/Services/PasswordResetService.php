<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Mailer;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use DateTimeImmutable;

final class PasswordResetService
{
    private const TTL_MINUTES = 60;

    private UserRepository $users;
    private PasswordResetRepository $resets;
    private Mailer $mailer;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->resets = new PasswordResetRepository();
        $this->mailer = Mailer::fromEnv();
    }

    public function request(string $email, string $baseUrl): void
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expires = (new DateTimeImmutable('+' . self::TTL_MINUTES . ' minutes'))->format('Y-m-d H:i:s');

        $this->resets->create($user->id, hash('sha256', $token), $expires);

        $link = $baseUrl . '/reset-password/' . $token;
        $this->mailer->sendHtml($email, 'Reset hasła w VetClinic', $this->emailHtml($user->firstName, $link));
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

    private function emailHtml(string $firstName, string $link): string
    {
        $name = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="pl">
<body style="margin:0;padding:0;background:#eef1fa;font-family:Arial,Helvetica,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef1fa;padding:32px 0;">
    <tr><td align="center">
      <table role="presentation" width="520" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e7f1;">
        <tr><td style="background:#117a6d;padding:24px 32px;">
          <span style="color:#ffffff;font-size:22px;font-weight:800;letter-spacing:-.01em;">VetClinic</span>
        </td></tr>
        <tr><td style="padding:32px;">
          <h1 style="margin:0 0 12px;font-size:22px;color:#0e1b2a;">Reset hasła</h1>
          <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#25313f;">Cześć {$name},</p>
          <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#25313f;">otrzymaliśmy prośbę o zmianę hasła do Twojego konta w VetClinic. Kliknij przycisk poniżej, aby ustawić nowe hasło. Link jest ważny przez 60&nbsp;minut.</p>
          <p style="margin:0 0 28px;">
            <a href="{$url}" style="display:inline-block;background:#117a6d;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px;padding:14px 28px;border-radius:12px;">Ustaw nowe hasło</a>
          </p>
          <p style="margin:0 0 8px;font-size:13px;color:#5b6675;">Jeśli przycisk nie działa, skopiuj ten adres do przeglądarki:</p>
          <p style="margin:0 0 24px;font-size:13px;word-break:break-all;"><a href="{$url}" style="color:#2563eb;">{$url}</a></p>
          <p style="margin:0;font-size:13px;line-height:1.6;color:#8a94a3;">Jeśli to nie Ty prosiłeś o zmianę hasła, zignoruj tę wiadomość — Twoje hasło pozostanie bez zmian.</p>
        </td></tr>
        <tr><td style="background:#f5f7fd;padding:18px 32px;border-top:1px solid #e2e7f1;">
          <span style="font-size:12px;color:#8a94a3;">© 2026 VetClinic — system zarządzania kliniką weterynaryjną.</span>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
