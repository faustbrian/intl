<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Country;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Symfony\Component\Intl\Exception\MissingResourceException;

describe('Country', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid country code string', function (): void {
            // Arrange
            $validCountryCode = 'US';

            // Act
            $country = Country::createFromString($validCountryCode);

            // Assert
            expect($country->alpha2)->toBe('US');
            expect($country->alpha3)->toBe('USA');
            expect($country->localized)->toBe('United States');
        });

        test('creates country with all properties populated', function (): void {
            // Arrange
            $countryCode = 'GB';

            // Act
            $country = Country::createFromString($countryCode);

            // Assert
            expect($country->alpha2)->toBe('GB');
            expect($country->alpha3)->toBe('GBR');
            expect($country->localized)->toBe('United Kingdom');
        });

        test('returns correct string representation via toString method', function (): void {
            // Arrange
            $country = Country::createFromString('US');

            // Act
            $result = $country->toString();

            // Assert
            expect($result)->toBe('US');
        });

        test('returns correct string representation via __toString method', function (): void {
            // Arrange
            $country = Country::createFromString('US');

            // Act
            $result = (string) $country;

            // Assert
            expect($result)->toBe('US');
        });

        test('__toString returns alpha2 code', function (): void {
            // Arrange
            $country = Country::createFromString('DE');

            // Act
            $stringValue = $country->__toString();

            // Assert
            expect($stringValue)->toBe('DE');
        });

        test('compares two equal countries correctly', function (): void {
            // Arrange
            $country1 = Country::createFromString('FR');
            $country2 = Country::createFromString('FR');

            // Act
            $result = $country1->isEqualTo($country2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('string concatenation uses __toString', function (): void {
            // Arrange
            $country = Country::createFromString('CA');

            // Act
            $result = 'Country code: '.$country;

            // Assert
            expect($result)->toBe('Country code: CA');
        });

        test('creates countries with various valid alpha-2 codes', function (string $code, string $alpha3, string $name): void {
            // Arrange & Act
            $country = Country::createFromString($code);

            // Assert
            expect($country->alpha2)->toBe($code);
            expect($country->alpha3)->toBe($alpha3);
            expect($country->localized)->toBe($name);
        })->with([
            ['JP', 'JPN', 'Japan'],
            ['AU', 'AUS', 'Australia'],
            ['BR', 'BRA', 'Brazil'],
            ['IN', 'IND', 'India'],
            ['CN', 'CHN', 'China'],
        ]);
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid country code string', function (): void {
            // Arrange
            $invalidCountryCode = 'XX';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($invalidCountryCode))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for lowercase country code', function (): void {
            // Arrange
            $lowercaseCode = 'us';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($lowercaseCode))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for alpha-3 code instead of alpha-2', function (): void {
            // Arrange
            $alpha3Code = 'USA';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($alpha3Code))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for numeric country code', function (): void {
            // Arrange
            $numericCode = '999';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($numericCode))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for empty string', function (): void {
            // Arrange
            $emptyString = '';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($emptyString))
                ->toThrow(MissingResourceException::class);
        });

        test('compares two different countries as not equal', function (): void {
            // Arrange
            $country1 = Country::createFromString('US');
            $country2 = Country::createFromString('GB');

            // Act
            $result = $country1->isEqualTo($country2);

            // Assert
            expect($result)->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles single space as invalid', function (): void {
            // Arrange
            $spaceString = ' ';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($spaceString))
                ->toThrow(MissingResourceException::class);
        });

        test('handles special characters as invalid', function (): void {
            // Arrange
            $specialChars = '@@';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($specialChars))
                ->toThrow(MissingResourceException::class);
        });

        test('handles single character code as invalid', function (): void {
            // Arrange
            $singleChar = 'U';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($singleChar))
                ->toThrow(MissingResourceException::class);
        });

        test('handles four character code as invalid', function (): void {
            // Arrange
            $fourChars = 'USAA';

            // Act & Assert
            expect(fn (): Country => Country::createFromString($fourChars))
                ->toThrow(MissingResourceException::class);
        });

        test('isEqualTo uses toString method for comparison', function (): void {
            // Arrange
            $country1 = Country::createFromString('IT');
            $country2 = Country::createFromString('IT');

            // Act
            $result = $country1->isEqualTo($country2);

            // Assert
            expect($result)->toBeTrue();
            expect($country1->toString())->toBe($country2->toString());
        });

        test('compares countries with same alpha2 but created differently', function (): void {
            // Arrange
            $country1 = Country::createFromString('ES');
            $country2 = new Country('Spain', 'ES', 'ESP');

            // Act
            $result = $country1->isEqualTo($country2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('__toString can be used in array key context', function (): void {
            // Arrange
            $country1 = Country::createFromString('NL');
            $country2 = Country::createFromString('BE');
            $data = [];

            // Act
            $data[(string) $country1] = 'Netherlands';
            $data[(string) $country2] = 'Belgium';

            // Assert
            expect($data)->toHaveKey('NL');
            expect($data)->toHaveKey('BE');
            expect($data['NL'])->toBe('Netherlands');
            expect($data['BE'])->toBe('Belgium');
        });
    });

    describe('Data Casting', function (): void {
        test('dataCastUsing returns Cast instance', function (): void {
            // Arrange & Act
            $cast = Country::dataCastUsing();

            // Assert
            expect($cast)->toBeInstanceOf(Cast::class);
        });

        test('Cast instance can cast string to Country', function (): void {
            // Arrange
            $cast = Country::dataCastUsing();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $value = 'FR';

            // Act
            $result = $cast->cast($property, $value, [], $context);

            // Assert
            expect($result)->toBeInstanceOf(Country::class);
            expect($result->alpha2)->toBe('FR');
            expect($result->alpha3)->toBe('FRA');
        });

        test('Cast instance handles numeric string conversion', function (): void {
            // Arrange
            $cast = Country::dataCastUsing();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $value = 123;

            // Act & Assert
            expect(fn (): mixed => $cast->cast($property, $value, [], $context))
                ->toThrow(MissingResourceException::class);
        });

        test('Cast implementation casts value to string before processing', function (): void {
            // Arrange
            $cast = Country::dataCastUsing();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $stringable = new class() implements Stringable
            {
                public function __toString(): string
                {
                    return 'SE';
                }
            };

            // Act
            $result = $cast->cast($property, $stringable, [], $context);

            // Assert
            expect($result)->toBeInstanceOf(Country::class);
            expect($result->alpha2)->toBe('SE');
        });
    });
});
