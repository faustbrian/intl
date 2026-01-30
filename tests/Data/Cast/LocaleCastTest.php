<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Locale;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Tests\Fakes\CastData;

describe('LocaleCast', function (): void {
    describe('Happy Paths', function (): void {
        test('casts valid locale code to Locale instance', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => 'fi_FI']);

            // Assert
            expect($actual->localeCode)
                ->toBeInstanceOf(Locale::class)
                ->and($actual->localeCode->value)->toBe('fi_FI')
                ->and($actual->localeCode->localized)->toBe('Finnish (Finland)');
        });

        test('casts common locale codes correctly', function (string $localeCode, string $expectedLocalized): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => $localeCode]);

            // Assert
            expect($actual->localeCode)
                ->toBeInstanceOf(Locale::class)
                ->and($actual->localeCode->value)->toBe($localeCode)
                ->and($actual->localeCode->localized)->toBe($expectedLocalized);
        })->with([
            ['en_US', 'English (United States)'],
            ['en_GB', 'English (United Kingdom)'],
            ['de_DE', 'German (Germany)'],
            ['fr_FR', 'French (France)'],
            ['es_ES', 'Spanish (Spain)'],
            ['ja_JP', 'Japanese (Japan)'],
            ['zh_CN', 'Chinese (China)'],
        ]);

        test('casts language-only locale codes', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => 'en']);

            // Assert
            expect($actual->localeCode)
                ->toBeInstanceOf(Locale::class)
                ->and($actual->localeCode->value)->toBe('en');
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null when value is empty string', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => '']);

            // Assert
            expect($actual->localeCode)->toBeNull();
        });

        test('returns null when value is string zero', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => '0']);

            // Assert
            expect($actual->localeCode)->toBeNull();
        });

        test('returns null when value is null', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => null]);

            // Assert
            expect($actual->localeCode)->toBeNull();
        });

        test('returns null when value is integer', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => 123]);

            // Assert
            expect($actual->localeCode)->toBeNull();
        });

        test('returns null when value is boolean', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => false]);

            // Assert
            expect($actual->localeCode)->toBeNull();
        });

        test('returns null when value is array', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => ['en_US']]);

            // Assert
            expect($actual->localeCode)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('throws exception for invalid locale code format', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::from(['localeCode' => 'invalid_locale_code']))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for non-existent locale', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::from(['localeCode' => 'xx_XX']))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for malformed locale string', function (): void {
            // Arrange & Act & Assert
            expect(fn (): CastData => CastData::from(['localeCode' => 'en_US_EXTRA']))
                ->toThrow(MissingResourceException::class);
        });

        test('handles locale with script code', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => 'zh_Hans_CN']);

            // Assert
            expect($actual->localeCode)
                ->toBeInstanceOf(Locale::class)
                ->and($actual->localeCode->value)->toBe('zh_Hans_CN');
        });

        test('handles special characters in valid locale codes', function (): void {
            // Arrange & Act
            $actual = CastData::from(['localeCode' => 'sr_Cyrl_RS']);

            // Assert
            expect($actual->localeCode)
                ->toBeInstanceOf(Locale::class)
                ->and($actual->localeCode->value)->toBe('sr_Cyrl_RS');
        });
    });
});
