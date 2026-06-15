<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Database;
use App\Services\LoyaltyService;
use PDOException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class LoyaltyServiceTest extends TestCase
{
    private LoyaltyService $service;

    protected function setUp(): void
    {
        try {
            Database::connection();
        } catch (RuntimeException | PDOException $e) {
            self::markTestSkipped('Baza danych jest niedostępna: ' . $e->getMessage());
        }

        $this->service = new LoyaltyService();
    }

    public function testDiscountForPointsIsNonNegativeAndMonotonic(): void
    {
        $low = $this->service->discountForPoints(1, 0);
        $high = $this->service->discountForPoints(1, 1_000_000);

        self::assertGreaterThanOrEqual(0.0, $low);
        self::assertGreaterThanOrEqual($low, $high);
    }

    public function testForClinicReturnsSettingsAndTiers(): void
    {
        $data = $this->service->forClinic(1);

        self::assertArrayHasKey('settings', $data);
        self::assertArrayHasKey('tiers', $data);
        self::assertArrayHasKey('points_per', $data['settings']);
        self::assertIsArray($data['tiers']);
    }

    public function testSaveSettingsRejectsInvalidInput(): void
    {
        $byPoints = $this->service->saveSettings(1, 'abc', '10');
        self::assertFalse($byPoints['ok']);

        $byAmount = $this->service->saveSettings(1, '1', '0');
        self::assertFalse($byAmount['ok']);
    }

    public function testAddTierRejectsOutOfRangePercent(): void
    {
        $result = $this->service->addTier(1, '500', '200');

        self::assertFalse($result['ok']);
    }

    public function testRemoveMissingTierReportsFailure(): void
    {
        $result = $this->service->removeTier(2_000_000_000, 1);

        self::assertFalse($result['ok']);
        self::assertSame('Nie znaleziono progu.', $result['message']);
    }
}
