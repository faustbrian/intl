<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Language;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Symfony\Component\Intl\Exception\MissingResourceException;

it('creates from valid language code string', function (): void {
    $validLanguageCode = 'en';
    $languageCode = Language::createFromString($validLanguageCode);

    expect($languageCode->toString())->toEqual($validLanguageCode);
});

it('throws exception for invalid language code string', function (): void {
    $invalidLanguageCode = 'xx';
    Language::createFromString($invalidLanguageCode);
})->throws(MissingResourceException::class);

it('returns correct localized string representation', function (): void {
    $validLanguageCode = 'en';
    $languageCode = Language::createFromString($validLanguageCode);

    expect($languageCode->localized)->toEqual('English');
});

it('returns correct string representation', function (): void {
    $validLanguageCode = 'en';
    $languageCode = Language::createFromString($validLanguageCode);

    expect($languageCode->toString())->toEqual($validLanguageCode);
});

describe('Language Value Object', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid language code string', function (): void {
            $language = Language::createFromString('en');

            expect($language->value)->toBe('en')
                ->and($language->localized)->toBe('English')
                ->and($language->toString())->toBe('en');
        });

        test('creates from different valid language codes', function (string $code, string $name): void {
            $language = Language::createFromString($code);

            expect($language->value)->toBe($code)
                ->and($language->localized)->toBe($name);
        })->with([
            ['es', 'Spanish'],
            ['fr', 'French'],
            ['de', 'German'],
            ['ja', 'Japanese'],
            ['zh', 'Chinese'],
        ]);

        test('converts to string via __toString magic method', function (): void {
            $language = Language::createFromString('en');

            expect((string) $language)->toBe('en');
        });

        test('compares equality with identical language codes', function (): void {
            $language1 = Language::createFromString('en');
            $language2 = Language::createFromString('en');

            expect($language1->isEqualTo($language2))->toBeTrue();
        });

        test('casts string value using dataCastUsing', function (): void {
            $cast = Language::dataCastUsing();
            $property = mock(DataProperty::class);
            $context = mock(CreationContext::class);

            $result = $cast->cast($property, 'en', [], $context);

            expect($result)->toBeInstanceOf(Language::class)
                ->and($result->value)->toBe('en')
                ->and($result->localized)->toBe('English');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid language code string', function (): void {
            Language::createFromString('invalid_code');
        })->throws(MissingResourceException::class);

        test('throws exception for empty language code', function (): void {
            Language::createFromString('');
        })->throws(MissingResourceException::class);

        test('throws exception for numeric language code', function (): void {
            Language::createFromString('123');
        })->throws(MissingResourceException::class);
    });

    describe('Edge Cases', function (): void {
        test('compares equality with different language codes', function (): void {
            $language1 = Language::createFromString('en');
            $language2 = Language::createFromString('es');

            expect($language1->isEqualTo($language2))->toBeFalse();
        });

        test('handles language codes with regional variants', function (): void {
            $language = Language::createFromString('en');

            expect($language->value)->toBe('en')
                ->and($language->localized)->toBe('English');
        });

        test('handles less common language codes', function (): void {
            $language = Language::createFromString('ga');

            expect($language->value)->toBe('ga')
                ->and($language->localized)->toBe('Irish');
        });

        test('toString returns same value as value property', function (): void {
            $language = Language::createFromString('fr');

            expect($language->toString())->toBe($language->value);
        });

        test('__toString returns same value as toString method', function (): void {
            $language = Language::createFromString('de');

            expect((string) $language)->toBe($language->toString());
        });

        test('casts numeric string value using dataCastUsing', function (): void {
            $cast = Language::dataCastUsing();
            $property = mock(DataProperty::class);
            $context = mock(CreationContext::class);

            expect(fn (): mixed => $cast->cast($property, '999', [], $context))
                ->toThrow(MissingResourceException::class);
        });

        test('compares language with itself', function (): void {
            $language = Language::createFromString('ja');

            expect($language->isEqualTo($language))->toBeTrue();
        });
    });
});
