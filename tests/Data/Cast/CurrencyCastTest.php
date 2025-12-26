<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\Currency;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Tests\Fakes\CastData;

describe('CurrencyCast', function (): void {
    describe('Happy Paths', function (): void {
        test('casts valid currency code to Currency object', function (): void {
            $actual = CastData::from([
                'currencyCode' => 'EUR',
            ]);

            expect($actual->currencyCode)
                ->toBeInstanceOf(Currency::class)
                ->and($actual->currencyCode->code)->toBe('EUR');
        });

        test('casts USD currency code', function (): void {
            $actual = CastData::from([
                'currencyCode' => 'USD',
            ]);

            expect($actual->currencyCode)
                ->toBeInstanceOf(Currency::class)
                ->and($actual->currencyCode->code)->toBe('USD');
        });

        test('casts GBP currency code', function (): void {
            $actual = CastData::from([
                'currencyCode' => 'GBP',
            ]);

            expect($actual->currencyCode)
                ->toBeInstanceOf(Currency::class)
                ->and($actual->currencyCode->code)->toBe('GBP');
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null for invalid currency code', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => 'INVALID',
            ]))->toThrow(MissingResourceException::class);
        });

        test('returns null for non-string value - integer', function (): void {
            $actual = CastData::from([
                'currencyCode' => 123,
            ]);

            expect($actual->currencyCode)->toBeNull();
        });

        test('returns null for non-string value - array', function (): void {
            $actual = CastData::from([
                'currencyCode' => ['EUR'],
            ]);

            expect($actual->currencyCode)->toBeNull();
        });

        test('returns null for non-string value - boolean', function (): void {
            $actual = CastData::from([
                'currencyCode' => true,
            ]);

            expect($actual->currencyCode)->toBeNull();
        });

        test('returns null for non-string value - object', function (): void {
            $actual = CastData::from([
                'currencyCode' => new stdClass(),
            ]);

            expect($actual->currencyCode)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty string', function (): void {
            $actual = CastData::from([
                'currencyCode' => '',
            ]);

            expect($actual->currencyCode)->toBeNull();
        });

        test('returns null for string zero', function (): void {
            $actual = CastData::from([
                'currencyCode' => '0',
            ]);

            expect($actual->currencyCode)->toBeNull();
        });

        test('returns null when property is null', function (): void {
            $actual = CastData::from([
                'currencyCode' => null,
            ]);

            expect($actual->currencyCode)->toBeNull();
        });

        test('handles lowercase currency code', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => 'eur',
            ]))->toThrow(MissingResourceException::class);
        });

        test('handles whitespace-only string', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => '   ',
            ]))->toThrow(MissingResourceException::class);
        });

        test('handles currency code with leading whitespace', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => ' EUR',
            ]))->toThrow(MissingResourceException::class);
        });

        test('handles currency code with trailing whitespace', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => 'EUR ',
            ]))->toThrow(MissingResourceException::class);
        });

        test('handles numeric string currency code', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => '123',
            ]))->toThrow(MissingResourceException::class);
        });

        test('handles special characters', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => '$$$',
            ]))->toThrow(MissingResourceException::class);
        });

        test('handles very long string', function (): void {
            expect(fn (): CastData => CastData::from([
                'currencyCode' => str_repeat('A', 100),
            ]))->toThrow(MissingResourceException::class);
        });
    });
});
