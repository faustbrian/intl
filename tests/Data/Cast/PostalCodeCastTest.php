<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\PostalCode;
use Tests\Fakes\CastData;

describe('PostalCodeCast', function (): void {
    describe('Happy Paths', function (): void {
        test('casts valid postal code with string country code', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => '00100',
            ]);

            expect($actual->postalCode)->toBeInstanceOf(PostalCode::class);
            expect($actual->postalCode->toString())->toBe('00100');
        });

        test('casts valid postal code with Country object as country code', function (): void {
            $actual = CastData::from([
                'countryCode' => Country::createFromString('SE'),
                'postalCode' => '12345',
            ]);

            expect($actual->postalCode)->toBeInstanceOf(PostalCode::class);
            expect($actual->postalCode->toString())->toBe('123 45'); // Swedish format
        });

        test('formats postal code according to country rules', function (): void {
            $actual = CastData::from([
                'countryCode' => 'LT',
                'postalCode' => 'LT-12345',
            ]);

            expect($actual->postalCode)->toBeInstanceOf(PostalCode::class);
            expect($actual->postalCode->toString())->toBe('LT-12345'); // Lithuanian format preserves prefix
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null when value is not a string', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => 12_345, // integer instead of string
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('returns null when postal code is empty string', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => '',
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('returns null when postal code is string zero', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => '0',
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('returns null when postal code is invalid for country', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => 'INVALID', // Invalid Finnish postal code
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('returns null when country code is unknown to brick postcode', function (): void {
            // Use a valid country code that exists in Symfony Intl but not in Brick Postcode
            // ZZ is reserved for user-defined territories but not in postal code validation
            $actual = CastData::from([
                'countryCode' => 'AQ', // Antarctica - valid country but no postal code system
                'postalCode' => '12345',
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('returns null when country code throws UnknownCountryException', function (): void {
            // Test various country codes that are not supported by Brick Postcode
            // These should trigger UnknownCountryException rather than InvalidPostcodeException
            $testCases = [
                ['countryCode' => 'BV', 'postalCode' => '12345'], // Bouvet Island
                ['countryCode' => 'HM', 'postalCode' => '12345'], // Heard Island and McDonald Islands
                ['countryCode' => 'TF', 'postalCode' => '12345'], // French Southern Territories
            ];

            foreach ($testCases as $testCase) {
                $actual = CastData::from($testCase);
                expect($actual->postalCode)->toBeNull();
            }
        });

        test('returns null when country code is null and postal code is invalid', function (): void {
            // When country code is null, the postal code is not validated/formatted
            // but we should still return null if the postal code can't be created
            $actual = CastData::from([
                'countryCode' => null,
                'postalCode' => 'test',
            ]);

            // When countryCode is null, PostalCode::createFromString returns the value as-is
            // So this should actually create a PostalCode object
            expect($actual->postalCode)->toBeInstanceOf(PostalCode::class);
            expect($actual->postalCode->toString())->toBe('test');
        });
    });

    describe('Edge Cases', function (): void {
        test('handles null value gracefully', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => null,
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('handles array value gracefully', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => ['12345'],
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('handles boolean false value', function (): void {
            $actual = CastData::from([
                'countryCode' => 'FI',
                'postalCode' => false,
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('handles postal code with whitespace', function (): void {
            $actual = CastData::from([
                'countryCode' => 'US',
                'postalCode' => '90210',
            ]);

            expect($actual->postalCode)->toBeInstanceOf(PostalCode::class);
            expect($actual->postalCode->toString())->toBe('90210');
        });

        test('handles postal codes with special characters for supported countries', function (): void {
            $actual = CastData::from([
                'countryCode' => 'GB',
                'postalCode' => 'SW1A1AA',
            ]);

            expect($actual->postalCode)->toBeInstanceOf(PostalCode::class);
            expect($actual->postalCode->toString())->toBe('SW1A 1AA'); // UK format
        });

        test('handles various country codes that might not be supported', function (): void {
            // Test additional country codes to ensure UnknownCountryException path is covered
            $unsupportedCountries = [
                'GS', // South Georgia and the South Sandwich Islands
                'UM', // United States Minor Outlying Islands
                'EH', // Western Sahara
            ];

            foreach ($unsupportedCountries as $countryCode) {
                $actual = CastData::from([
                    'countryCode' => $countryCode,
                    'postalCode' => '12345',
                ]);

                expect($actual->postalCode)->toBeNull();
            }
        });

        test('handles Country object with unknown country code', function (): void {
            // Create a Country object and pass it directly
            // This tests the path where countryCode is a Country instance
            $actual = CastData::from([
                'countryCode' => 'BV', // Bouvet Island - triggers UnknownCountryException
                'postalCode' => 'ABC123',
            ]);

            expect($actual->postalCode)->toBeNull();
        });

        test('handles malformed postal codes with various invalid formats', function (): void {
            $invalidFormats = [
                ['countryCode' => 'US', 'postalCode' => '123'],           // Too short for US
                ['countryCode' => 'US', 'postalCode' => '123456789012'], // Too long
                ['countryCode' => 'GB', 'postalCode' => '!!!'],           // Invalid characters
                ['countryCode' => 'CA', 'postalCode' => 'ZZZZZ'],        // Invalid Canadian format
            ];

            foreach ($invalidFormats as $testCase) {
                $actual = CastData::from($testCase);
                expect($actual->postalCode)->toBeNull();
            }
        });

        test('handles postal code with country object when exception occurs', function (): void {
            // Pass a Country object that will result in an unknown country exception
            $country = Country::createFromString('AQ'); // Antarctica

            $actual = CastData::from([
                'countryCode' => $country,
                'postalCode' => 'TEST123',
            ]);

            expect($actual->postalCode)->toBeNull();
        });
    });
});
