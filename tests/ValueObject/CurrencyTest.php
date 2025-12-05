<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Currency;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Symfony\Component\Intl\Exception\MissingResourceException;

describe('Currency', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid currency code string', function (): void {
            $currency = Currency::createFromString('USD');

            expect($currency->name)->toEqual('US Dollar');
            expect($currency->symbol)->toEqual('$');
            expect($currency->fractionDigits)->toEqual(2);
            expect($currency->roundingIncrement)->toEqual(0);
            expect($currency->cashFractionDigits)->toEqual(2);
            expect($currency->cashRoundingIncrement)->toEqual(0);
            expect($currency->numericCode)->toEqual(840);
            expect($currency->toString())->toEqual('USD');
        });

        test('creates currency with all properties correctly populated', function (): void {
            $currency = Currency::createFromString('EUR');

            expect($currency->code)->toEqual('EUR');
            expect($currency->name)->toEqual('Euro');
            expect($currency->symbol)->toEqual('€');
            expect($currency->fractionDigits)->toBeInt();
            expect($currency->roundingIncrement)->toBeInt();
            expect($currency->cashFractionDigits)->toBeInt();
            expect($currency->cashRoundingIncrement)->toBeInt();
            expect($currency->numericCode)->toEqual(978);
        });

        test('converts to string using __toString magic method', function (): void {
            $currency = Currency::createFromString('GBP');

            expect((string) $currency)->toEqual('GBP');
        });

        test('converts to string using toString method', function (): void {
            $currency = Currency::createFromString('JPY');

            expect($currency->toString())->toEqual('JPY');
        });

        test('compares equality with another currency successfully', function (): void {
            $currency1 = Currency::createFromString('CAD');
            $currency2 = Currency::createFromString('CAD');

            expect($currency1->isEqualTo($currency2))->toBeTrue();
        });

        test('detects inequality with different currency', function (): void {
            $currency1 = Currency::createFromString('AUD');
            $currency2 = Currency::createFromString('NZD');

            expect($currency1->isEqualTo($currency2))->toBeFalse();
        });

        test('casts string value to Currency via dataCastUsing', function (): void {
            $cast = Currency::dataCastUsing();

            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);

            $result = $cast->cast($property, 'CHF', [], $context);

            expect($result)->toBeInstanceOf(Currency::class);
            expect($result->code)->toEqual('CHF');
            expect($result->name)->toEqual('Swiss Franc');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid currency code string', function (): void {
            Currency::createFromString('INVALID');
        })->throws(MissingResourceException::class);

        test('throws exception for empty currency code', function (): void {
            Currency::createFromString('');
        })->throws(MissingResourceException::class);

        test('throws exception for non-existent currency code', function (): void {
            Currency::createFromString('ZZZ');
        })->throws(MissingResourceException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles currency without numeric code gracefully', function (): void {
            $currency = Currency::createFromString('ARL'); // Historic currency without numeric code

            expect($currency->code)->toEqual('ARL');
            expect($currency->name)->toEqual('Argentine Peso Ley (1970–1983)');
            expect($currency->numericCode)->toBeNull();
        });

        test('handles historic currency codes', function (): void {
            $currency = Currency::createFromString('ARL');

            expect($currency->code)->toEqual('ARL');
            expect($currency->numericCode)->toBeNull();
        });

        test('casts string value with special characters', function (): void {
            $cast = Currency::dataCastUsing();

            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);

            $result = $cast->cast($property, 'BRL', [], $context);

            expect($result)->toBeInstanceOf(Currency::class);
            expect($result->code)->toEqual('BRL');
            expect($result->symbol)->toEqual('R$');
        });

        test('handles currencies with zero fraction digits', function (): void {
            $currency = Currency::createFromString('JPY'); // Japanese Yen has 0 fraction digits

            expect($currency->code)->toEqual('JPY');
            expect($currency->fractionDigits)->toEqual(0);
        });

        test('compares different instances of same currency', function (): void {
            $currency1 = Currency::createFromString('USD');
            $currency2 = Currency::createFromString('USD');

            expect($currency1->isEqualTo($currency2))->toBeTrue();
            expect($currency1)->not->toBe($currency2); // Different instances
        });
    });
});
