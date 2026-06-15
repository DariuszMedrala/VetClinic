<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Invoice;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    private function make(string $subtotal, string $total, string $status = 'pending', ?string $payment = null): Invoice
    {
        return new Invoice(
            1,
            10,
            'FV/2026/001',
            $status,
            $payment,
            new DateTimeImmutable('2026-01-15'),
            null,
            'Reksio',
            'Pies',
            'Jan Kowalski',
            120,
            $subtotal,
            $total,
        );
    }

    #[DataProvider('moneyProvider')]
    public function testMoneyFormatsPolishCurrency(string $input, string $expected): void
    {
        self::assertSame($expected, Invoice::money($input));
    }

    public static function moneyProvider(): array
    {
        return [
            ['0', '0,00 zł'],
            ['9.5', '9,50 zł'],
            ['1234.5', '1 234,50 zł'],
            ['1000000', '1 000 000,00 zł'],
        ];
    }

    public function testDiscountIsDetectedWhenTotalIsLower(): void
    {
        $invoice = $this->make('200.00', '180.00');

        self::assertTrue($invoice->hasDiscount());
        self::assertSame(10, $invoice->discountPercent());
        self::assertSame('−20,00 zł', $invoice->discountLabel());
    }

    public function testNoDiscountWhenTotalEqualsSubtotal(): void
    {
        $invoice = $this->make('150.00', '150.00');

        self::assertFalse($invoice->hasDiscount());
        self::assertSame(0, $invoice->discountPercent());
    }

    public function testDiscountPercentGuardsAgainstZeroSubtotal(): void
    {
        $invoice = $this->make('0', '0');

        self::assertSame(0, $invoice->discountPercent());
    }

    public function testStatusAndTotalLabels(): void
    {
        $paid = $this->make('100.00', '100.00', 'paid', 'card');

        self::assertSame('Opłacona', $paid->statusLabel());
        self::assertSame('badge--uptodate', $paid->statusBadge());
        self::assertSame('Karta', $paid->paymentLabel());
        self::assertSame('100,00 zł', $paid->totalLabel());
        self::assertFalse($paid->isPending());
    }

    public function testPendingState(): void
    {
        $pending = $this->make('100.00', '100.00', 'pending');

        self::assertTrue($pending->isPending());
        self::assertSame('Oczekuje', $pending->statusLabel());
        self::assertSame('—', $pending->paymentLabel());
    }
}
