<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Language;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Tests\Fakes\CastData;

describe('LanguageCast', function (): void {
    describe('Happy Paths', function (): void {
        test('casts valid 2-letter language code to Language instance', function (): void {
            $actual = CastData::from([
                'languageCode' => 'fi',
            ]);

            expect($actual->languageCode)->toBeInstanceOf(Language::class);
            expect($actual->languageCode->value)->toBe('fi');
        });

        test('casts common language codes correctly', function (string $code): void {
            $actual = CastData::from([
                'languageCode' => $code,
            ]);

            expect($actual->languageCode)->toBeInstanceOf(Language::class);
            expect($actual->languageCode->value)->toBe($code);
        })->with([
            'en',
            'es',
            'fr',
            'de',
            'ja',
            'zh',
            'ar',
            'ru',
        ]);
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid language code', function (): void {
            CastData::from([
                'languageCode' => 'invalid',
            ]);
        })->throws(MissingResourceException::class);

        test('throws exception for non-existent 2-letter code', function (): void {
            CastData::from([
                'languageCode' => 'zz',
            ]);
        })->throws(MissingResourceException::class);

        test('throws exception for 3-letter language code', function (): void {
            // Symfony Intl only supports ISO 639-1 (2-letter) codes
            CastData::from([
                'languageCode' => 'eng',
            ]);
        })->throws(MissingResourceException::class);

        test('throws exception for numeric string that is not zero', function (): void {
            CastData::from([
                'languageCode' => '123',
            ]);
        })->throws(MissingResourceException::class);
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty string', function (): void {
            $actual = CastData::from([
                'languageCode' => '',
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('returns null for string zero', function (): void {
            $actual = CastData::from([
                'languageCode' => '0',
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('returns null for null value', function (): void {
            $actual = CastData::from([
                'languageCode' => null,
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('returns null for integer value', function (): void {
            $actual = CastData::from([
                'languageCode' => 123,
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('returns null for boolean true', function (): void {
            $actual = CastData::from([
                'languageCode' => true,
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('returns null for boolean false', function (): void {
            $actual = CastData::from([
                'languageCode' => false,
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('returns null for array value', function (): void {
            $actual = CastData::from([
                'languageCode' => ['fi'],
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('returns null for object value', function (): void {
            $actual = CastData::from([
                'languageCode' => (object) ['code' => 'fi'],
            ]);

            expect($actual->languageCode)->toBeNull();
        });

        test('throws exception for whitespace-only string', function (): void {
            // Whitespace-only strings are NOT considered empty by the cast
            // They will be passed to Language::createFromString and should throw
            CastData::from([
                'languageCode' => '   ',
            ]);
        })->throws(MissingResourceException::class);

        test('throws exception for uppercase language code', function (): void {
            // Language codes are case-sensitive - must be lowercase
            CastData::from([
                'languageCode' => 'FI',
            ]);
        })->throws(MissingResourceException::class);
    });
});
