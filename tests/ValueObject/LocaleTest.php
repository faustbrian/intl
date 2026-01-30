<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Locale;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Symfony\Component\Intl\Exception\MissingResourceException;

describe('Locale', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid locale string', function (): void {
            // Arrange
            $validLocale = 'en_US';

            // Act
            $locale = Locale::createFromString($validLocale);

            // Assert
            expect($locale->toString())->toEqual($validLocale);
            expect($locale->value)->toEqual($validLocale);
        });

        test('returns correct localized string representation', function (): void {
            // Arrange
            $validLocale = 'en_US';

            // Act
            $locale = Locale::createFromString($validLocale);

            // Assert
            expect($locale->localized)->toEqual('English (United States)');
        });

        test('returns correct string representation via toString method', function (): void {
            // Arrange
            $validLocale = 'en_US';

            // Act
            $locale = Locale::createFromString($validLocale);

            // Assert
            expect($locale->toString())->toEqual($validLocale);
        });

        test('casts to string via __toString magic method', function (): void {
            // Arrange
            $validLocale = 'fr_FR';

            // Act
            $locale = Locale::createFromString($validLocale);
            $stringValue = (string) $locale;

            // Assert
            expect($stringValue)->toEqual($validLocale);
        });

        test('compares equal locales correctly', function (): void {
            // Arrange
            $locale1 = Locale::createFromString('en_US');
            $locale2 = Locale::createFromString('en_US');

            // Act
            $result = $locale1->isEqualTo($locale2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('creates locale via data cast', function (): void {
            // Arrange
            $cast = Locale::dataCastUsing();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);

            // Act
            $result = $cast->cast($property, 'de_DE', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toEqual('de_DE');
            expect($result->localized)->toEqual('German (Germany)');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid locale string', function (): void {
            // Arrange
            $invalidLocale = 'invalid-locale';

            // Act & Assert
            Locale::createFromString($invalidLocale);
        })->throws(MissingResourceException::class);

        test('throws exception when casting invalid locale string', function (): void {
            // Arrange
            $cast = Locale::dataCastUsing();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);

            // Act & Assert
            $cast->cast($property, 'invalid-code', [], $context);
        })->throws(MissingResourceException::class);

        test('identifies non-equal locales correctly', function (): void {
            // Arrange
            $locale1 = Locale::createFromString('en_US');
            $locale2 = Locale::createFromString('fr_FR');

            // Act
            $result = $locale1->isEqualTo($locale2);

            // Assert
            expect($result)->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles different locale formats', function (): void {
            // Arrange
            $locales = ['en', 'en_GB', 'pt_BR', 'zh_Hans_CN'];

            // Act & Assert
            foreach ($locales as $localeCode) {
                $locale = Locale::createFromString($localeCode);
                expect($locale->value)->toEqual($localeCode);
                expect($locale->localized)->toBeString();
            }
        });

        test('casts numeric string to locale', function (): void {
            // Arrange
            $cast = Locale::dataCastUsing();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);

            // Act
            $result = $cast->cast($property, 'en', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toEqual('en');
        });

        test('compares locales with different cases', function (): void {
            // Arrange
            $locale1 = Locale::createFromString('en_US');
            $locale2 = Locale::createFromString('en_GB');

            // Act
            $result = $locale1->isEqualTo($locale2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('string representation matches value property', function (): void {
            // Arrange
            $localeCode = 'ja_JP';

            // Act
            $locale = Locale::createFromString($localeCode);

            // Assert
            expect((string) $locale)->toEqual($locale->value);
            expect($locale->toString())->toEqual($locale->value);
        });
    });
});
