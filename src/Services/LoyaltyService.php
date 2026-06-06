<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\LoyaltyRepository;

final class LoyaltyService
{
    private LoyaltyRepository $loyalty;

    public function __construct()
    {
        $this->loyalty = new LoyaltyRepository();
    }

    public function forClinic(int $clinicId): array
    {
        return [
            'settings' => $this->loyalty->settings($clinicId),
            'tiers' => $this->loyalty->tiers($clinicId),
        ];
    }

    public function discountForPoints(int $clinicId, int $points): float
    {
        return $this->loyalty->discountPercentFor($clinicId, $points);
    }

    public function awardForInvoice(int $invoiceId): void
    {
        $this->loyalty->awardForInvoice($invoiceId);
    }

    public function saveSettings(int $clinicId, string $pointsPer, string $perAmount): array
    {
        if (!ctype_digit($pointsPer) || (int) $pointsPer < 0) {
            return ['ok' => false, 'message' => 'Liczba punktów musi być liczbą całkowitą nieujemną.'];
        }

        if (!is_numeric($perAmount) || (float) $perAmount <= 0) {
            return ['ok' => false, 'message' => 'Kwota musi być liczbą większą od zera.'];
        }

        $this->loyalty->saveSettings($clinicId, (int) $pointsPer, $perAmount);

        return ['ok' => true, 'message' => 'Zasady naliczania punktów zostały zapisane.'];
    }

    public function addTier(int $clinicId, string $minPoints, string $percent): array
    {
        if (!ctype_digit($minPoints)) {
            return ['ok' => false, 'message' => 'Próg punktowy musi być liczbą całkowitą.'];
        }

        if (!is_numeric($percent) || (float) $percent < 0 || (float) $percent > 100) {
            return ['ok' => false, 'message' => 'Zniżka musi być wartością od 0 do 100.'];
        }

        $this->loyalty->addTier($clinicId, (int) $minPoints, $percent);

        return ['ok' => true, 'message' => 'Próg zniżki został zapisany.'];
    }

    public function removeTier(int $id, int $clinicId): array
    {
        return $this->loyalty->deleteTier($id, $clinicId)
            ? ['ok' => true, 'message' => 'Próg zniżki został usunięty.']
            : ['ok' => false, 'message' => 'Nie znaleziono progu.'];
    }
}
