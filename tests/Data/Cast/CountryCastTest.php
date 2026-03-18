<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Country;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Tests\Fakes\CastData;

describe('CountryCast', function (): void {
    describe('Happy Paths', function (): void {
        test('casts valid ISO 3166-1 alpha-2 country code to Country object', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => 'FI']);

            // Assert
            expect($actual->countryCode)->toBeInstanceOf(Country::class);
            expect($actual->countryCode->alpha2)->toBe('FI');
        });

        test('casts US country code correctly', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => 'US']);

            // Assert
            expect($actual->countryCode)->toBeInstanceOf(Country::class);
            expect($actual->countryCode->alpha2)->toBe('US');
        });

        test('casts GB country code correctly', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => 'GB']);

            // Assert
            expect($actual->countryCode)->toBeInstanceOf(Country::class);
            expect($actual->countryCode->alpha2)->toBe('GB');
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null when value is empty string', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => '']);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });

        test('returns null when value is string zero', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => '0']);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });

        test('returns null when value is null', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => null]);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });

        test('returns null when value is integer', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => 123]);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });

        test('returns null when value is array', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => ['FI']]);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });

        test('returns null when value is boolean', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => true]);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });

        test('throws exception for invalid country code', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::create(['countryCode' => 'INVALID']))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for lowercase valid country code', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::create(['countryCode' => 'fi']))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for three letter code instead of two', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::create(['countryCode' => 'FIN']))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for numeric string country code', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::create(['countryCode' => '999']))
                ->toThrow(MissingResourceException::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles single space string as invalid', function (): void {
            expect(CastData::create(['countryCode' => ' '])->countryCode)->toBeNull();
        });

        test('handles special characters as invalid', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::create(['countryCode' => '@@']))
                ->toThrow(MissingResourceException::class);
        });

        test('casts various valid country codes', function (string $code): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => $code]);

            // Assert
            expect($actual->countryCode)->toBeInstanceOf(Country::class);
            expect($actual->countryCode->alpha2)->toBe($code);
        })->with([
            'DE', // Germany
            'FR', // France
            'JP', // Japan
            'CA', // Canada
            'AU', // Australia
            'BR', // Brazil
            'IN', // India
            'CN', // China
        ]);

        test('returns null when value is float', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => 3.14]);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });

        test('returns null when value is object', function (): void {
            // Arrange & Act
            $actual = CastData::create(['countryCode' => new stdClass()]);

            // Assert
            expect($actual->countryCode)->toBeNull();
        });
    });
});
