<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Pet;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PetTest extends TestCase
{
    private function make(string $sex, ?string $weight, ?DateTimeImmutable $birth): Pet
    {
        return new Pet(
            1,
            2,
            3,
            'Pies',
            'Reksio',
            'Owczarek',
            $sex,
            $birth,
            $weight,
            'Jan Kowalski',
            '+48 600 700 800',
            120,
            null,
        );
    }

    public function testSexLabels(): void
    {
        self::assertSame('Samiec', $this->make('male', null, null)->sexLabel());
        self::assertSame('Samica', $this->make('female', null, null)->sexLabel());
        self::assertSame('Nieznana', $this->make('other', null, null)->sexLabel());
    }

    public function testWeightLabel(): void
    {
        self::assertSame('12,5 kg', $this->make('male', '12.5', null)->weightLabel());
        self::assertSame('—', $this->make('male', null, null)->weightLabel());
    }

    public function testAgeLabelReturnsDashWithoutBirthDate(): void
    {
        self::assertSame('—', $this->make('male', null, null)->ageLabel());
    }

    public function testAgeLabelUsesMonthsUnderOneYear(): void
    {
        $birth = (new DateTimeImmutable('today'))->modify('-3 months');

        self::assertSame('3 mies.', $this->make('male', null, $birth)->ageLabel());
    }

    public function testAgeLabelPolishPluralisation(): void
    {
        $cases = [
            ['-1 years', '1 rok'],
            ['-2 years', '2 lata'],
            ['-3 years', '3 lata'],
            ['-5 years', '5 lat'],
            ['-12 years', '12 lat'],
            ['-22 years', '22 lata'],
        ];

        foreach ($cases as [$modify, $expected]) {
            $birth = (new DateTimeImmutable('today'))->modify($modify);

            self::assertSame($expected, $this->make('male', null, $birth)->ageLabel(), $modify);
        }
    }

    public function testFromRowMapsColumns(): void
    {
        $pet = Pet::fromRow([
            'id' => '7',
            'client_id' => '4',
            'species_id' => '1',
            'species' => 'Kot',
            'name' => 'Mruczek',
            'breed' => null,
            'sex' => 'female',
            'birth_date' => null,
            'weight_kg' => '4.20',
            'owner_name' => 'Anna Nowak',
            'owner_phone' => null,
            'loyalty_points' => '15',
            'photo_path' => null,
        ]);

        self::assertSame(7, $pet->id);
        self::assertSame('Kot', $pet->speciesName);
        self::assertNull($pet->breed);
        self::assertSame('Samica', $pet->sexLabel());
        self::assertSame('4,2 kg', $pet->weightLabel());
    }
}
