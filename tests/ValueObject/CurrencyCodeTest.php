<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\CurrencyCode;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Symfony\Component\Intl\Exception\MissingResourceException;

describe('CurrencyCode', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid currency code string', function (): void {
            $validCurrencyCode = 'USD';
            $currency = CurrencyCode::createFromString($validCurrencyCode);

            expect($currency->toString())->toEqual($validCurrencyCode);
        });

        test('returns correct localized string representation', function (): void {
            $validCurrencyCode = 'USD';
            $currency = CurrencyCode::createFromString($validCurrencyCode);

            expect($currency->localized)->toEqual('US Dollar');
        });

        test('returns correct string representation via toString', function (): void {
            $validCurrencyCode = 'USD';
            $currency = CurrencyCode::createFromString($validCurrencyCode);

            expect($currency->toString())->toEqual($validCurrencyCode);
        });

        test('converts to string via __toString method', function (): void {
            $currency = CurrencyCode::createFromString('EUR');

            expect((string) $currency)->toBe('EUR');
        });

        test('supports string casting in concatenation', function (): void {
            $currency = CurrencyCode::createFromString('GBP');

            expect('Currency: '.$currency)->toBe('Currency: GBP');
        });

        test('creates from EUR currency code', function (): void {
            $currency = CurrencyCode::createFromString('EUR');

            expect($currency->value)->toBe('EUR')
                ->and($currency->localized)->toBe('Euro');
        });

        test('creates from GBP currency code', function (): void {
            $currency = CurrencyCode::createFromString('GBP');

            expect($currency->value)->toBe('GBP')
                ->and($currency->localized)->toBe('British Pound');
        });

        test('creates from JPY currency code', function (): void {
            $currency = CurrencyCode::createFromString('JPY');

            expect($currency->value)->toBe('JPY')
                ->and($currency->localized)->toBe('Japanese Yen');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid currency code string', function (): void {
            $invalidCurrencyCode = 'XXX';

            CurrencyCode::createFromString($invalidCurrencyCode);
        })->throws(MissingResourceException::class);

        test('throws exception for non-existent currency code', function (): void {
            CurrencyCode::createFromString('ZZZ');
        })->throws(MissingResourceException::class);

        test('throws exception for lowercase currency code', function (): void {
            CurrencyCode::createFromString('usd');
        })->throws(MissingResourceException::class);

        test('throws exception for numeric currency code', function (): void {
            CurrencyCode::createFromString('123');
        })->throws(MissingResourceException::class);

        test('throws exception for special characters', function (): void {
            CurrencyCode::createFromString('$$$');
        })->throws(MissingResourceException::class);
    });

    describe('Edge Cases', function (): void {
        test('throws exception for empty string', function (): void {
            CurrencyCode::createFromString('');
        })->throws(MissingResourceException::class);

        test('throws exception for whitespace-only string', function (): void {
            CurrencyCode::createFromString('   ');
        })->throws(MissingResourceException::class);

        test('throws exception for currency code with leading whitespace', function (): void {
            CurrencyCode::createFromString(' EUR');
        })->throws(MissingResourceException::class);

        test('throws exception for currency code with trailing whitespace', function (): void {
            CurrencyCode::createFromString('EUR ');
        })->throws(MissingResourceException::class);

        test('throws exception for very long string', function (): void {
            CurrencyCode::createFromString(str_repeat('A', 100));
        })->throws(MissingResourceException::class);

        test('throws exception for two-letter code', function (): void {
            CurrencyCode::createFromString('US');
        })->throws(MissingResourceException::class);

        test('throws exception for four-letter code', function (): void {
            CurrencyCode::createFromString('USDD');
        })->throws(MissingResourceException::class);
    });

    describe('isEqualTo method', function (): void {
        describe('Happy Paths', function (): void {
            test('returns true for identical currency codes', function (): void {
                $currency1 = CurrencyCode::createFromString('USD');
                $currency2 = CurrencyCode::createFromString('USD');

                expect($currency1->isEqualTo($currency2))->toBeTrue();
            });

            test('returns true when comparing same instance', function (): void {
                $currency = CurrencyCode::createFromString('EUR');

                expect($currency->isEqualTo($currency))->toBeTrue();
            });
        });

        describe('Sad Paths', function (): void {
            test('returns false for different currency codes', function (): void {
                $currency1 = CurrencyCode::createFromString('USD');
                $currency2 = CurrencyCode::createFromString('EUR');

                expect($currency1->isEqualTo($currency2))->toBeFalse();
            });

            test('returns false comparing USD with GBP', function (): void {
                $usd = CurrencyCode::createFromString('USD');
                $gbp = CurrencyCode::createFromString('GBP');

                expect($usd->isEqualTo($gbp))->toBeFalse();
            });
        });

        describe('Edge Cases', function (): void {
            test('handles comparison with different localized names', function (): void {
                $currency1 = CurrencyCode::createFromString('USD');
                $currency2 = CurrencyCode::createFromString('USD');

                expect($currency1->localized)->toBe($currency2->localized)
                    ->and($currency1->isEqualTo($currency2))->toBeTrue();
            });
        });
    });

    describe('dataCastUsing method', function (): void {
        describe('Happy Paths', function (): void {
            test('returns Cast instance', function (): void {
                $cast = CurrencyCode::dataCastUsing();

                expect($cast)->toBeInstanceOf(Cast::class);
            });

            test('cast converts valid currency code string to CurrencyCode', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'USD', [], $context);

                expect($result)
                    ->toBeInstanceOf(CurrencyCode::class)
                    ->and($result->value)->toBe('USD')
                    ->and($result->localized)->toBe('US Dollar');
            });

            test('cast converts EUR currency code', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'EUR', [], $context);

                expect($result)
                    ->toBeInstanceOf(CurrencyCode::class)
                    ->and($result->value)->toBe('EUR')
                    ->and($result->localized)->toBe('Euro');
            });

            test('cast converts GBP currency code', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'GBP', [], $context);

                expect($result)
                    ->toBeInstanceOf(CurrencyCode::class)
                    ->and($result->value)->toBe('GBP');
            });

            test('cast converts integer to string before processing', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, 978, [], $context))
                    ->toThrow(MissingResourceException::class);
            });
        });

        describe('Sad Paths', function (): void {
            test('cast throws exception for invalid currency code', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $cast->cast($property, 'INVALID', [], $context);
            })->throws(MissingResourceException::class);

            test('cast throws exception for non-existent code', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $cast->cast($property, 'ZZZ', [], $context);
            })->throws(MissingResourceException::class);

            test('cast throws exception for lowercase currency code', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $cast->cast($property, 'usd', [], $context);
            })->throws(MissingResourceException::class);
        });

        describe('Edge Cases', function (): void {
            test('cast handles numeric string', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '123', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles empty string', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles whitespace-only string', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '   ', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles currency code with leading whitespace', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, ' EUR', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles currency code with trailing whitespace', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, 'EUR ', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles special characters', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '$$$', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles very long string', function (): void {
                $cast = CurrencyCode::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, str_repeat('A', 100), [], $context))
                    ->toThrow(MissingResourceException::class);
            });
        });
    });
});
