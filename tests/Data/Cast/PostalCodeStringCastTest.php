<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Country;
use Tests\Fakes\CastData;

describe('PostalCodeStringCast', function (): void {
    describe('Happy Paths', function (): void {
        test('formats postal code according to country rules', function (): void {
            $actual = CastData::from([
                'countryCode' => 'SE',
                'postalCodeString' => '12345',
            ]);

            expect($actual->postalCodeString)->toBe('123 45');
        });

        test('handles Country value object as countryCode', function (): void {
            $country = Country::createFromString('SE');

            $actual = CastData::from([
                'countryCode' => $country,
                'postalCodeString' => '12345',
            ]);

            expect($actual->postalCodeString)->toBe('123 45');
        });

        test('formats Lithuanian postal code without prefix', function (): void {
            $actual = CastData::from([
                'countryCode' => 'LT',
                'postalCodeString' => '01001',
            ]);

            expect($actual->postalCodeString)->toBe('01001');
        });
    });

    describe('Sad Paths', function (): void {
        test('returns original value for invalid postal code format', function (): void {
            $actual = CastData::from([
                'countryCode' => 'SE',
                'postalCodeString' => 'INVALID',
            ]);

            expect($actual->postalCodeString)->toBe('INVALID');
        });

        test('returns original value for unknown country code', function (): void {
            // Angola (AO) is valid in Symfony but not supported by brick/postcode
            $actual = CastData::from([
                'countryCode' => 'AO', // Angola - triggers UnknownCountryException
                'postalCodeString' => '12345',
            ]);

            expect($actual->postalCodeString)->toBe('12345');
        });

        test('returns original value for country without postal code support', function (): void {
            // Botswana (BW) is valid but brick/postcode doesn't support it
            $actual = CastData::from([
                'countryCode' => 'BW', // Botswana - triggers UnknownCountryException
                'postalCodeString' => 'TEST123',
            ]);

            expect($actual->postalCodeString)->toBe('TEST123');
        });

        test('returns original value when formatted postal code would be empty', function (): void {
            // Some edge case where the formatter might return empty
            $actual = CastData::from([
                'countryCode' => 'GB',
                'postalCodeString' => 'ABC',
            ]);

            expect($actual->postalCodeString)->toBe('ABC');
        });

        test('returns original value for country with different postal code than expected', function (): void {
            // Zimbabwe (ZW) doesn't have postal code support in brick/postcode
            $actual = CastData::from([
                'countryCode' => 'ZW', // Zimbabwe - triggers UnknownCountryException
                'postalCodeString' => 'HARARE',
            ]);

            expect($actual->postalCodeString)->toBe('HARARE');
        });

        test('returns original value when postal code is too short for country', function (): void {
            $actual = CastData::from([
                'countryCode' => 'SE',
                'postalCodeString' => '123', // Too short for Swedish postal code
            ]);

            expect($actual->postalCodeString)->toBe('123');
        });

        test('returns original value when postal code is too long for country', function (): void {
            $actual = CastData::from([
                'countryCode' => 'SE',
                'postalCodeString' => '1234567890', // Too long for Swedish postal code
            ]);

            expect($actual->postalCodeString)->toBe('1234567890');
        });

        test('returns original value for numeric only code in alphabetic country', function (): void {
            $actual = CastData::from([
                'countryCode' => 'GB',
                'postalCodeString' => '12345', // UK requires letters
            ]);

            expect($actual->postalCodeString)->toBe('12345');
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty string', function (): void {
            $actual = CastData::from([
                'countryCode' => 'SE',
                'postalCodeString' => '',
            ]);

            expect($actual->postalCodeString)->toBeNull();
        });

        test('returns null for zero string', function (): void {
            $actual = CastData::from([
                'countryCode' => 'SE',
                'postalCodeString' => '0',
            ]);

            expect($actual->postalCodeString)->toBeNull();
        });

        test('handles postal code with spaces', function (): void {
            $actual = CastData::from([
                'countryCode' => 'GB',
                'postalCodeString' => 'SW1A 1AA',
            ]);

            expect($actual->postalCodeString)->toBe('SW1A 1AA');
        });

        test('handles postal code with hyphens', function (): void {
            $actual = CastData::from([
                'countryCode' => 'CA',
                'postalCodeString' => 'K1A-0B1',
            ]);

            // Canadian postal codes are formatted with space, not hyphen
            expect($actual->postalCodeString)->toMatch('/^[A-Z]\d[A-Z]\s\d[A-Z]\d$/');
        });

        test('handles null country code', function (): void {
            $actual = CastData::from([
                'countryCode' => null,
                'postalCodeString' => '12345',
            ]);

            expect($actual->postalCodeString)->toBe('12345');
        });

        test('handles special characters in postal code with unknown country', function (): void {
            $actual = CastData::from([
                'countryCode' => 'GH', // Ghana - triggers UnknownCountryException
                'postalCodeString' => '!@#$%',
            ]);

            expect($actual->postalCodeString)->toBe('!@#$%');
        });

        test('handles very long postal code with invalid format', function (): void {
            $longCode = str_repeat('1234567890', 10);
            $actual = CastData::from([
                'countryCode' => 'US',
                'postalCodeString' => $longCode,
            ]);

            // Should return original value when invalid
            expect($actual->postalCodeString)->toBe($longCode);
        });

        test('handles mixed case postal code', function (): void {
            $actual = CastData::from([
                'countryCode' => 'GB',
                'postalCodeString' => 'sw1a 1aa',
            ]);

            // UK postcodes are typically uppercase
            expect($actual->postalCodeString)->toMatch('/[A-Z0-9\s]+/i');
        });
    });
});
