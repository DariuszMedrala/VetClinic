<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Mailer
{
    public function __construct(
        private string $host,
        private int $port,
        private string $fromEmail,
        private string $fromName,
        private string $username = '',
        private string $password = '',
        private string $encryption = '',
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            getenv('MAIL_HOST') ?: 'mailpit',
            (int) (getenv('MAIL_PORT') ?: 1025),
            getenv('MAIL_FROM') ?: 'no-reply@vetclinic.pl',
            getenv('MAIL_FROM_NAME') ?: 'VetClinic',
            getenv('MAIL_USERNAME') ?: '',
            getenv('MAIL_PASSWORD') ?: '',
            strtolower((string) (getenv('MAIL_ENCRYPTION') ?: '')),
        );
    }

    public function sendHtml(string $to, string $subject, string $html): bool
    {
        $remote = ($this->encryption === 'ssl' ? 'ssl://' : '') . $this->host;
        $socket = @fsockopen($remote, $this->port, $errno, $errstr, 10);

        if ($socket === false) {
            error_log("Mailer: brak połączenia z {$this->host}:{$this->port} ($errstr)");

            return false;
        }

        stream_set_timeout($socket, 15);

        try {
            $this->expect($socket, 220);
            $this->command($socket, 'EHLO vetclinic.local', 250);

            if ($this->encryption === 'tls') {
                $this->command($socket, 'STARTTLS', 220);

                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new RuntimeException('Nie udało się nawiązać połączenia TLS.');
                }

                $this->command($socket, 'EHLO vetclinic.local', 250);
            }

            if ($this->username !== '') {
                $this->command($socket, 'AUTH LOGIN', 334);
                $this->command($socket, base64_encode($this->username), 334);
                $this->command($socket, base64_encode($this->password), 235);
            }

            $this->command($socket, 'MAIL FROM:<' . $this->fromEmail . '>', 250);
            $this->command($socket, 'RCPT TO:<' . $to . '>', 250);
            $this->command($socket, 'DATA', 354);
            fwrite($socket, $this->message($to, $subject, $html) . "\r\n.\r\n");
            $this->expect($socket, 250);
            $this->command($socket, 'QUIT', 221);
        } catch (RuntimeException $exception) {
            error_log('Mailer: ' . $exception->getMessage());
            fclose($socket);

            return false;
        }

        fclose($socket);

        return true;
    }

    private function command($socket, string $command, int $expected): void
    {
        fwrite($socket, $command . "\r\n");
        $this->expect($socket, $expected);
    }

    private function expect($socket, int $code): void
    {
        $response = '';

        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;

            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }

        if ((int) substr($response, 0, 3) !== $code) {
            throw new RuntimeException('Nieoczekiwana odpowiedź SMTP: ' . trim($response));
        }
    }

    private function message(string $to, string $subject, string $html): string
    {
        $headers = [
            'From: ' . $this->encodeHeader($this->fromName) . ' <' . $this->fromEmail . '>',
            'To: <' . $to . '>',
            'Subject: ' . $this->encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'Date: ' . date('r'),
        ];

        return implode("\r\n", $headers) . "\r\n\r\n" . $this->normalizeBody($html);
    }

    private function encodeHeader(string $value): string
    {
        if (preg_match('/[^\x20-\x7e]/', $value) !== 1) {
            return $value;
        }

        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function normalizeBody(string $html): string
    {
        $html = (string) preg_replace('/\r\n|\r|\n/', "\r\n", $html);

        return (string) preg_replace('/^\./m', '..', $html);
    }
}
