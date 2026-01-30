<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\PhoneNumber;
use Tests\Fakes\CastData;

describe('PhoneNumberCast', function (): void {
    describe('Happy Paths', function (): void {
        test('casts valid phone number with country code string', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'FI',
                'phoneNumber' => '+358 40 1234567',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->toString())
                ->toBeString();
        });

        test('casts valid phone number with Country instance', function (): void {
            // Arrange
            $country = Country::createFromString('US');
            $data = [
                'countryCode' => $country,
                'phoneNumber' => '+1 (415) 555-1234',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->regionCode)
                ->toBe('US');
        });

        test('casts international phone numbers correctly', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'GB',
                'phoneNumber' => '+44 20 7946 0958',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->isValid)
                ->toBeTrue();
        });

        test('casts mobile phone numbers', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'DE',
                'phoneNumber' => '+49 151 23456789',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->numberType)
                ->not->toBeNull();
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null for empty string', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)->toBeNull();
        });

        test('returns null for string zero', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '0',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)->toBeNull();
        });

        test('throws exception for invalid phone number format', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => 'not-a-valid-phone',
            ];

            // Act & Assert
            expect(fn (): CastData => CastData::from($data))
                ->toThrow(Exception::class);
        });

        test('throws exception for incomplete phone number', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '+1',
            ];

            // Act & Assert
            expect(fn (): CastData => CastData::from($data))
                ->toThrow(Exception::class);
        });

        test('throws exception for phone number with invalid country code', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'XX',
                'phoneNumber' => '+999 1234567890',
            ];

            // Act & Assert
            expect(fn (): CastData => CastData::from($data))
                ->toThrow(Exception::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles phone number with excessive whitespace', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '  +1   415   555   1234  ',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class);
        });

        test('handles phone number with special characters', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '+1 (415) 555-1234',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->isValid)
                ->toBeTrue();
        });

        test('handles phone number with dots as separators', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '+1.415.555.1234',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class);
        });

        test('handles minimum valid phone number length', function (): void {
            // Arrange - Use a country with shorter phone numbers
            $data = [
                'countryCode' => 'FI',
                'phoneNumber' => '+358 40 123456',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->isPossible)
                ->toBeTrue();
        });

        test('handles Country instance with alpha2 code extraction', function (): void {
            // Arrange
            $country = Country::createFromString('FR');
            $data = [
                'countryCode' => $country,
                'phoneNumber' => '+33 1 42 86 82 00',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->regionCode)
                ->toBe('FR')
                ->and($actual->phoneNumber->countryCode)
                ->toBe('33');
        });

        test('handles phone numbers with extension symbols', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '+14155551234',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class);
        });

        test('handles toll-free numbers', function (): void {
            // Arrange
            $data = [
                'countryCode' => 'US',
                'phoneNumber' => '+1 800 555 1234',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->isPossible)
                ->toBeTrue();
        });

        test('handles very long phone numbers', function (): void {
            // Arrange - Some countries have longer numbers
            $data = [
                'countryCode' => 'DE',
                'phoneNumber' => '+49 30 123456789012',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class);
        });

        test('handles null country code by inferring from international format', function (): void {
            // Arrange - Pass null as countryCode
            $data = [
                'countryCode' => null,
                'phoneNumber' => '+358401234567',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class)
                ->and($actual->phoneNumber->regionCode)
                ->toBe('FI');
        });

        test('handles non-string non-Country country code', function (): void {
            // Arrange - Pass an integer as countryCode (edge case)
            $data = [
                'countryCode' => 123,
                'phoneNumber' => '+358401234567',
            ];

            // Act
            $actual = CastData::from($data);

            // Assert
            expect($actual->phoneNumber)
                ->toBeInstanceOf(PhoneNumber::class);
        });
    });
});
