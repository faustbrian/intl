<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brick\Postcode\InvalidPostcodeException;
use Brick\Postcode\UnknownCountryException;
use Cline\Intl\ValueObjects\PostalCode;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

describe('PostalCode', function (): void {
    describe('Happy Paths', function (): void {
        test('formats finnish postal codes', function (): void {
            // Arrange
            $postalCode = '12345';
            $countryCode = 'FI';

            // Act
            $result = PostalCode::createFromString($postalCode, $countryCode);

            // Assert
            expect($result->toString())->toBe('12345');
        });

        test('formats swedish postal codes', function (): void {
            // Arrange
            $postalCode = '12345';
            $countryCode = 'SE';

            // Act
            $result = PostalCode::createFromString($postalCode, $countryCode);

            // Assert
            expect($result->toString())->toBe('123 45');
        });

        test('formats latvian postal codes', function (): void {
            // Arrange
            $postalCode = '1234';
            $countryCode = 'LV';

            // Act
            $result = PostalCode::createFromString($postalCode, $countryCode);

            // Assert
            expect($result->toString())->toBe('LV-1234');
        });

        test('formats lithuanian postal codes', function (): void {
            // Arrange
            $postalCode = '12345';
            $countryCode = 'LT';

            // Act
            $result = PostalCode::createFromString($postalCode, $countryCode);

            // Assert
            expect($result->toString())->toBe('12345');
        });

        test('formats estonian postal codes', function (): void {
            // Arrange
            $postalCode = '12345';
            $countryCode = 'EE';

            // Act
            $result = PostalCode::createFromString($postalCode, $countryCode);

            // Assert
            expect($result->toString())->toBe('12345');
        });

        test('creates postal code without country code', function (): void {
            // Arrange
            $postalCode = '90210';

            // Act
            $result = PostalCode::createFromString($postalCode, null);

            // Assert
            expect($result->toString())->toBe('90210');
            expect($result->postalCode)->toBe('90210');
        });

        test('converts to string using __toString', function (): void {
            // Arrange
            $postalCode = PostalCode::createFromString('12345', 'FI');

            // Act
            $result = (string) $postalCode;

            // Assert
            expect($result)->toBe('12345');
        });

        test('converts to string using toString method', function (): void {
            // Arrange
            $postalCode = PostalCode::createFromString('12345', 'FI');

            // Act
            $result = $postalCode->toString();

            // Assert
            expect($result)->toBe('12345');
        });

        test('compares equal postal codes correctly', function (): void {
            // Arrange
            $postalCode1 = PostalCode::createFromString('12345', 'FI');
            $postalCode2 = PostalCode::createFromString('12345', 'FI');

            // Act
            $result = $postalCode1->isEqualTo($postalCode2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('compares different postal codes correctly', function (): void {
            // Arrange
            $postalCode1 = PostalCode::createFromString('12345', 'FI');
            $postalCode2 = PostalCode::createFromString('54321', 'FI');

            // Act
            $result = $postalCode1->isEqualTo($postalCode2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('casts value using dataCastUsing', function (): void {
            // Arrange
            $cast = PostalCode::dataCastUsing();
            $property = mock(DataProperty::class);
            $context = mock(CreationContext::class);
            $value = '90210';

            // Act
            $result = $cast->cast($property, $value, [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PostalCode::class);
            expect($result->toString())->toBe('90210');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid postal code format', function (): void {
            // Arrange
            $invalidPostalCode = 'INVALID';
            $countryCode = 'FI';

            // Act & Assert
            expect(fn (): PostalCode => PostalCode::createFromString($invalidPostalCode, $countryCode))
                ->toThrow(InvalidPostcodeException::class);
        });

        test('throws exception for unknown country code', function (): void {
            // Arrange
            $postalCode = '12345';
            $unknownCountryCode = 'XX';

            // Act & Assert
            expect(fn (): PostalCode => PostalCode::createFromString($postalCode, $unknownCountryCode))
                ->toThrow(UnknownCountryException::class);
        });

        test('compares postal codes with different formats as not equal', function (): void {
            // Arrange
            $postalCode1 = PostalCode::createFromString('12345', null);
            $postalCode2 = PostalCode::createFromString('12345', 'SE'); // formatted as '123 45'

            // Act
            $result = $postalCode1->isEqualTo($postalCode2);

            // Assert
            expect($result)->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty string postal code without country code', function (): void {
            // Arrange
            $emptyPostalCode = '';

            // Act
            $result = PostalCode::createFromString($emptyPostalCode, null);

            // Assert
            expect($result->toString())->toBe('');
        });

        test('handles whitespace in postal code without country code', function (): void {
            // Arrange
            $postalCode = '  12345  ';

            // Act
            $result = PostalCode::createFromString($postalCode, null);

            // Assert
            expect($result->toString())->toBe('  12345  ');
        });

        test('casts numeric value to postal code', function (): void {
            // Arrange
            $cast = PostalCode::dataCastUsing();
            $property = mock(DataProperty::class);
            $context = mock(CreationContext::class);
            $numericValue = 12_345;

            // Act
            $result = $cast->cast($property, $numericValue, [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PostalCode::class);
            expect($result->toString())->toBe('12345');
        });

        test('handles special characters in postal code without country code', function (): void {
            // Arrange
            $postalCode = 'ABC-123';

            // Act
            $result = PostalCode::createFromString($postalCode, null);

            // Assert
            expect($result->toString())->toBe('ABC-123');
        });

        test('compares postal codes created with and without country code', function (): void {
            // Arrange
            $postalCode1 = PostalCode::createFromString('12345', null);
            $postalCode2 = PostalCode::createFromString('12345', 'FI');

            // Act
            $result = $postalCode1->isEqualTo($postalCode2);

            // Assert
            expect($result)->toBeTrue();
        });
    });

    describe('Regressions', function (): void {
        test('maintains formatted postal code format when comparing', function (): void {
            // Arrange - Swedish postal codes are formatted with space
            $postalCode1 = PostalCode::createFromString('12345', 'SE');
            $postalCode2 = PostalCode::createFromString('12345', 'SE');

            // Act
            $result = $postalCode1->isEqualTo($postalCode2);

            // Assert
            expect($result)->toBeTrue();
            expect($postalCode1->toString())->toBe('123 45');
        });
    });
});
