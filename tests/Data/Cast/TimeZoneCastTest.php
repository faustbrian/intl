<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\TimeZone;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Tests\Fakes\CastData;

describe('TimeZoneCast', function (): void {
    describe('Happy Paths', function (): void {
        test('casts valid timezone string to TimeZone object', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 'Europe/Helsinki',
            ]);

            // Assert
            expect($actual->timeZone)->toBeInstanceOf(TimeZone::class)
                ->and($actual->timeZone->value)->toBe('Europe/Helsinki');
        });

        test('casts valid Asia timezone to TimeZone object', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 'Asia/Tokyo',
            ]);

            // Assert
            expect($actual->timeZone)->toBeInstanceOf(TimeZone::class)
                ->and($actual->timeZone->value)->toBe('Asia/Tokyo');
        });

        test('casts valid America timezone to TimeZone object', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 'America/New_York',
            ]);

            // Assert
            expect($actual->timeZone)->toBeInstanceOf(TimeZone::class)
                ->and($actual->timeZone->value)->toBe('America/New_York');
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null for empty string', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => '',
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('returns null for string zero', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => '0',
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('throws exception for invalid timezone string', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::create([
                'timeZone' => 'Invalid/Timezone',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for invalid timezone format', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::create([
                'timeZone' => 'not-a-timezone',
            ]))->toThrow(MissingResourceException::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for null value', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => null,
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('returns null for integer value', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 123,
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('returns null for float value', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 123.45,
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('returns null for boolean true', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => true,
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('returns null for boolean false', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => false,
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('returns null for array value', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => ['Europe/Helsinki'],
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('returns null for object value', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => (object) ['timezone' => 'Europe/Helsinki'],
            ]);

            // Assert
            expect($actual->timeZone)->toBeNull();
        });

        test('casts timezone string with whitespace correctly', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 'Europe/Helsinki',
            ]);

            // Assert
            expect($actual->timeZone)->toBeInstanceOf(TimeZone::class)
                ->and($actual->timeZone->value)->toBe('Europe/Helsinki');
        });

        test('handles timezone with underscores in name', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 'America/Port_of_Spain',
            ]);

            // Assert
            expect($actual->timeZone)->toBeInstanceOf(TimeZone::class)
                ->and($actual->timeZone->value)->toBe('America/Port_of_Spain');
        });

        test('handles timezone with multiple path segments', function (): void {
            // Arrange & Act
            $actual = CastData::create([
                'timeZone' => 'America/Argentina/Buenos_Aires',
            ]);

            // Assert
            expect($actual->timeZone)->toBeInstanceOf(TimeZone::class)
                ->and($actual->timeZone->value)->toBe('America/Argentina/Buenos_Aires');
        });
    });
});
