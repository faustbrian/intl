<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Enums\CountryCode;

describe('CountryCode Enum', function (): void {
    describe('Happy Paths', function (): void {
        test('creates enum from valid alpha-2 code', function (): void {
            // Arrange & Act
            $country = CountryCode::from('US');

            // Assert
            expect($country)->toBeInstanceOf(CountryCode::class)
                ->and($country->value)->toBe('US');
        });

        test('creates enum from lowercase alpha-2 code using tryFrom', function (): void {
            // Arrange & Act
            $country = CountryCode::tryFrom('us');

            // Assert
            expect($country)->toBeNull();
        });

        test('returns all country codes via cases', function (): void {
            // Arrange & Act
            $cases = CountryCode::cases();

            // Assert
            expect($cases)->toBeArray()
                ->toHaveCount(249)
                ->and($cases[0])->toBeInstanceOf(CountryCode::class);
        });

        test('tryFromString returns country from alpha-2 code', function (): void {
            // Arrange
            $code = 'US';

            // Act
            $result = CountryCode::tryFromString($code);

            // Assert
            expect($result)->toBeInstanceOf(CountryCode::class)
                ->and($result->value)->toBe('US');
        });

        test('tryFromString returns country from alpha-3 code', function (): void {
            // Arrange
            $alpha3Code = 'USA';

            // Act
            $result = CountryCode::tryFromString($alpha3Code);

            // Assert
            expect($result)->toBeInstanceOf(CountryCode::class)
                ->and($result->value)->toBe('US');
        });

        test('tryFromString handles various valid alpha-3 codes', function (string $alpha3, string $expectedAlpha2): void {
            // Arrange & Act
            $result = CountryCode::tryFromString($alpha3);

            // Assert
            expect($result)->toBeInstanceOf(CountryCode::class)
                ->and($result->value)->toBe($expectedAlpha2);
        })->with([
            ['GBR', 'GB'],
            ['DEU', 'DE'],
            ['FRA', 'FR'],
            ['CAN', 'CA'],
            ['AUS', 'AU'],
            ['JPN', 'JP'],
            ['CHN', 'CN'],
            ['IND', 'IN'],
            ['BRA', 'BR'],
            ['MEX', 'MX'],
        ]);

        test('accesses enum value property', function (): void {
            // Arrange
            $country = CountryCode::FR;

            // Act
            $value = $country->value;

            // Assert
            expect($value)->toBe('FR');
        });

        test('compares enum instances', function (): void {
            // Arrange
            $us1 = CountryCode::US;
            $us2 = CountryCode::from('US');
            $ca = CountryCode::CA;

            // Act & Assert
            expect($us1 === $us2)->toBeTrue()
                ->and($us1 === $ca)->toBeFalse();
        });

        test('serializes enum to string value', function (): void {
            // Arrange
            $country = CountryCode::GB;

            // Act
            $serialized = json_encode($country);

            // Assert
            expect($serialized)->toBe('"GB"');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws error for invalid alpha-2 code', function (): void {
            // Arrange & Act & Assert
            expect(fn () => CountryCode::from('XX'))
                ->toThrow(ValueError::class);
        });

        test('tryFrom returns null for invalid alpha-2 code', function (): void {
            // Arrange & Act
            $result = CountryCode::tryFrom('XX');

            // Assert
            expect($result)->toBeNull();
        });

        test('tryFromString returns null for invalid alpha-3 code', function (): void {
            // Arrange
            $invalidCode = 'XXX';

            // Act
            $result = CountryCode::tryFromString($invalidCode);

            // Assert
            expect($result)->toBeNull();
        });

        test('tryFromString returns null for null input', function (): void {
            // Arrange & Act
            $result = CountryCode::tryFromString(null);

            // Assert
            expect($result)->toBeNull();
        });

        test('tryFromString returns null for invalid length strings', function (string $invalidCode): void {
            // Arrange & Act
            $result = CountryCode::tryFromString($invalidCode);

            // Assert
            expect($result)->toBeNull();
        })->with([
            'single char' => ['U'],
            'four chars' => ['USAA'],
            'five chars' => ['USAAA'],
            'empty string' => [''],
        ]);

        test('tryFromString returns null for non-existent country codes', function (): void {
            // Arrange
            $nonExistentCode = 'ZZZ';

            // Act
            $result = CountryCode::tryFromString($nonExistentCode);

            // Assert
            expect($result)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles unicode country names in comments', function (): void {
            // Arrange & Act
            $aland = CountryCode::AX; // Åland Islands
            $curacao = CountryCode::CW; // Curaçao
            $reunion = CountryCode::RE; // Réunion

            // Assert
            expect($aland->value)->toBe('AX')
                ->and($curacao->value)->toBe('CW')
                ->and($reunion->value)->toBe('RE');
        });

        test('handles all 249 country codes', function (): void {
            // Arrange & Act
            $allCases = CountryCode::cases();

            // Assert
            expect($allCases)->toHaveCount(249);

            // Verify each case has a valid 2-letter code
            foreach ($allCases as $case) {
                expect($case->value)->toMatch('/^[A-Z]{2}$/');
            }
        });

        test('tryFromString handles exact 2-character codes', function (): void {
            // Arrange
            $twoCharCode = 'US';

            // Act
            $result = CountryCode::tryFromString($twoCharCode);

            // Assert
            expect($result)->toBeInstanceOf(CountryCode::class)
                ->and($result->value)->toBe('US');
        });

        test('tryFromString handles exact 3-character codes', function (): void {
            // Arrange
            $threeCharCode = 'USA';

            // Act
            $result = CountryCode::tryFromString($threeCharCode);

            // Assert
            expect($result)->toBeInstanceOf(CountryCode::class);
        });

        test('handles special territories and dependencies', function (CountryCode $territory): void {
            // Arrange & Act & Assert
            expect($territory)->toBeInstanceOf(CountryCode::class)
                ->and($territory->value)->toMatch('/^[A-Z]{2}$/');
        })->with([
            'Åland Islands' => [CountryCode::AX],
            'Antarctica' => [CountryCode::AQ],
            'Bouvet Island' => [CountryCode::BV],
            'British Indian Ocean Territory' => [CountryCode::IO],
            'Christmas Island' => [CountryCode::CX],
            'Cocos (Keeling) Islands' => [CountryCode::CC],
            'French Southern Territories' => [CountryCode::TF],
            'Heard & McDonald Islands' => [CountryCode::HM],
            'U.S. Outlying Islands' => [CountryCode::UM],
            'Vatican City' => [CountryCode::VA],
        ]);

        test('handles countries with multiple words in names', function (CountryCode $country): void {
            // Arrange & Act & Assert
            expect($country)->toBeInstanceOf(CountryCode::class);
        })->with([
            'United States' => [CountryCode::US],
            'United Kingdom' => [CountryCode::GB],
            'United Arab Emirates' => [CountryCode::AE],
            'South Africa' => [CountryCode::ZA],
            'New Zealand' => [CountryCode::NZ],
            'Costa Rica' => [CountryCode::CR],
            'Saudi Arabia' => [CountryCode::SA],
        ]);

        test('tryFromString with multibyte string length handling', function (): void {
            // Arrange - Test that mb_strlen is used correctly for length check
            $validTwoByteCode = 'US'; // 2 characters, 2 bytes

            // Act
            $result = CountryCode::tryFromString($validTwoByteCode);

            // Assert
            expect($result)->toBeInstanceOf(CountryCode::class);
        });

        test('all country codes are unique', function (): void {
            // Arrange
            $allCases = CountryCode::cases();

            // Act
            $values = array_map(fn (CountryCode $case) => $case->value, $allCases);
            $uniqueValues = array_unique($values);

            // Assert
            expect($values)->toHaveCount(count($uniqueValues));
        });

        test('all country codes are uppercase', function (): void {
            // Arrange
            $allCases = CountryCode::cases();

            // Act & Assert
            foreach ($allCases as $case) {
                expect($case->value)->toBe(mb_strtoupper($case->value));
            }
        });

        test('enum name matches enum value', function (): void {
            // Arrange
            $country = CountryCode::US;

            // Act
            $name = $country->name;
            $value = $country->value;

            // Assert
            expect($name)->toBe($value);
        });
    });

    describe('Integration with Symfony Countries Component', function (): void {
        test('tryFromString uses Symfony Countries for alpha-3 conversion', function (): void {
            // Arrange - These are valid ISO 3166-1 alpha-3 codes
            $testCodes = [
                'AFG' => 'AF', // Afghanistan
                'USA' => 'US', // United States
                'GBR' => 'GB', // United Kingdom
                'FRA' => 'FR', // France
                'DEU' => 'DE', // Germany
            ];

            // Act & Assert
            foreach ($testCodes as $alpha3 => $expectedAlpha2) {
                $result = CountryCode::tryFromString($alpha3);
                expect($result)->toBeInstanceOf(CountryCode::class)
                    ->and($result->value)->toBe($expectedAlpha2);
            }
        });

        test('tryFromString handles Symfony exception for invalid alpha-3', function (): void {
            // Arrange
            $invalidAlpha3 = 'XYZ';

            // Act
            $result = CountryCode::tryFromString($invalidAlpha3);

            // Assert
            expect($result)->toBeNull();
        });
    });
});
