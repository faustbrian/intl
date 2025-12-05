<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObjects\TimeZone;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Tests\Fakes\CastData;

describe('TimeZone', function (): void {
    describe('Happy Paths', function (): void {
        test('creates timezone from valid string', function (): void {
            $timezone = TimeZone::createFromString('Europe/Helsinki');

            expect($timezone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($timezone->value)->toBe('Europe/Helsinki')
                ->and($timezone->localized)->toBe('Eastern European Time (Helsinki)');
        });

        test('creates timezone from Pacific/Honolulu', function (): void {
            $timezone = TimeZone::createFromString('Pacific/Honolulu');

            expect($timezone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($timezone->value)->toBe('Pacific/Honolulu')
                ->and($timezone->localized)->toBe('Hawaii-Aleutian Time (Honolulu)');
        });

        test('creates timezone from America/New_York', function (): void {
            $timezone = TimeZone::createFromString('America/New_York');

            expect($timezone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($timezone->value)->toBe('America/New_York');
        });

        test('creates timezone from Asia/Tokyo', function (): void {
            $timezone = TimeZone::createFromString('Asia/Tokyo');

            expect($timezone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($timezone->value)->toBe('Asia/Tokyo');
        });

        test('returns value via toString method', function (): void {
            $timezone = TimeZone::createFromString('Europe/London');

            expect($timezone->toString())->toBe('Europe/London');
        });

        test('returns value via __toString magic method', function (): void {
            $timezone = TimeZone::createFromString('Europe/Paris');

            expect((string) $timezone)->toBe('Europe/Paris');
        });

        test('returns value when cast to string', function (): void {
            $timezone = TimeZone::createFromString('Australia/Sydney');

            expect((string) $timezone)->toBe('Australia/Sydney');
        });

        test('compares equal timezones correctly', function (): void {
            $timezone1 = TimeZone::createFromString('Europe/Berlin');
            $timezone2 = TimeZone::createFromString('Europe/Berlin');

            expect($timezone1->isEqualTo($timezone2))->toBeTrue();
        });

        test('casts valid timezone string to TimeZone object', function (): void {
            $data = CastData::from([
                'timeZone' => 'America/Chicago',
            ]);

            expect($data->timeZone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($data->timeZone->value)->toBe('America/Chicago');
        });

        test('casts Asia/Dubai timezone', function (): void {
            $data = CastData::from([
                'timeZone' => 'Asia/Dubai',
            ]);

            expect($data->timeZone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($data->timeZone->value)->toBe('Asia/Dubai');
        });

        test('casts Europe/London timezone', function (): void {
            $data = CastData::from([
                'timeZone' => 'Europe/London',
            ]);

            expect($data->timeZone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($data->timeZone->value)->toBe('Europe/London');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid timezone string', function (): void {
            TimeZone::createFromString('Invalid/TimeZone');
        })->throws(MissingResourceException::class);

        test('throws exception for completely invalid format', function (): void {
            TimeZone::createFromString('NotATimeZone');
        })->throws(MissingResourceException::class);

        test('throws exception for numeric timezone', function (): void {
            TimeZone::createFromString('12345');
        })->throws(MissingResourceException::class);

        test('compares different timezones correctly', function (): void {
            $timezone1 = TimeZone::createFromString('Europe/London');
            $timezone2 = TimeZone::createFromString('America/New_York');

            expect($timezone1->isEqualTo($timezone2))->toBeFalse();
        });

        test('throws exception when casting invalid timezone', function (): void {
            expect(fn (): CastData => CastData::from([
                'timeZone' => 'Invalid/TimeZone',
            ]))->toThrow(MissingResourceException::class);
        });

        test('returns null for non-string value - integer', function (): void {
            $data = CastData::from([
                'timeZone' => 12_345,
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for non-string value - array', function (): void {
            $data = CastData::from([
                'timeZone' => ['Europe/London'],
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for non-string value - boolean', function (): void {
            $data = CastData::from([
                'timeZone' => true,
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for non-string value - object', function (): void {
            $data = CastData::from([
                'timeZone' => new stdClass(),
            ]);

            expect($data->timeZone)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty string', function (): void {
            $data = CastData::from([
                'timeZone' => '',
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for string zero', function (): void {
            $data = CastData::from([
                'timeZone' => '0',
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null when property is null', function (): void {
            $data = CastData::from([
                'timeZone' => null,
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('handles timezone with underscore', function (): void {
            $timezone = TimeZone::createFromString('America/Los_Angeles');

            expect($timezone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($timezone->value)->toBe('America/Los_Angeles');
        });

        test('handles timezone with multiple path segments', function (): void {
            $timezone = TimeZone::createFromString('America/Argentina/Buenos_Aires');

            expect($timezone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($timezone->value)->toBe('America/Argentina/Buenos_Aires');
        });

        test('throws exception for lowercase timezone', function (): void {
            expect(fn (): TimeZone => TimeZone::createFromString('europe/london'))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for whitespace-only string', function (): void {
            expect(fn (): CastData => CastData::from([
                'timeZone' => '   ',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for timezone with leading whitespace', function (): void {
            expect(fn (): CastData => CastData::from([
                'timeZone' => ' Europe/London',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for timezone with trailing whitespace', function (): void {
            expect(fn (): CastData => CastData::from([
                'timeZone' => 'Europe/London ',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for special characters', function (): void {
            expect(fn (): CastData => CastData::from([
                'timeZone' => '@#$%',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for very long string', function (): void {
            expect(fn (): CastData => CastData::from([
                'timeZone' => str_repeat('A', 100),
            ]))->toThrow(MissingResourceException::class);
        });

        test('handles timezone with single path component', function (): void {
            $timezone = TimeZone::createFromString('Africa/Cairo');

            expect($timezone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($timezone->value)->toBe('Africa/Cairo')
                ->and($timezone->localized)->toBe('Eastern European Time (Cairo)');
        });

        test('compares timezones with same value but different instances', function (): void {
            $timezone1 = TimeZone::createFromString('Pacific/Auckland');
            $timezone2 = TimeZone::createFromString('Pacific/Auckland');

            expect($timezone1->isEqualTo($timezone2))->toBeTrue()
                ->and($timezone1 === $timezone2)->toBeFalse();
        });
    });

    describe('dataCastUsing method', function (): void {
        describe('Happy Paths', function (): void {
            test('returns Cast instance', function (): void {
                $cast = TimeZone::dataCastUsing();

                expect($cast)->toBeInstanceOf(Cast::class);
            });

            test('cast converts valid timezone string to TimeZone', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'Europe/Helsinki', [], $context);

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('Europe/Helsinki')
                    ->and($result->localized)->toBe('Eastern European Time (Helsinki)');
            });

            test('cast converts America/New_York timezone', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'America/New_York', [], $context);

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('America/New_York');
            });

            test('cast converts Asia/Tokyo timezone', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'Asia/Tokyo', [], $context);

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('Asia/Tokyo');
            });

            test('cast converts Pacific/Honolulu timezone', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'Pacific/Honolulu', [], $context);

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('Pacific/Honolulu')
                    ->and($result->localized)->toBe('Hawaii-Aleutian Time (Honolulu)');
            });

            test('cast handles integer by casting to string', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, 12_345, [], $context))
                    ->toThrow(MissingResourceException::class);
            });
        });

        describe('Sad Paths', function (): void {
            test('cast throws exception for invalid timezone string', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $cast->cast($property, 'Invalid/TimeZone', [], $context);
            })->throws(MissingResourceException::class);

            test('cast throws exception for non-existent timezone', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $cast->cast($property, 'NotATimeZone', [], $context);
            })->throws(MissingResourceException::class);

            test('cast throws exception for lowercase timezone', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $cast->cast($property, 'europe/london', [], $context);
            })->throws(MissingResourceException::class);

            test('cast throws exception for numeric string timezone', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $cast->cast($property, '12345', [], $context);
            })->throws(MissingResourceException::class);
        });

        describe('Edge Cases', function (): void {
            test('cast handles numeric string', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '999', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles empty string', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles whitespace-only string', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '   ', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles timezone with leading whitespace', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, ' Europe/London', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles timezone with trailing whitespace', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, 'Europe/London ', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles special characters', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, '@#$%', [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles very long string', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, str_repeat('A', 100), [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles timezone with underscore', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'America/Los_Angeles', [], $context);

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('America/Los_Angeles');
            });

            test('cast handles timezone with multiple path segments', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                $result = $cast->cast($property, 'America/Argentina/Buenos_Aires', [], $context);

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('America/Argentina/Buenos_Aires');
            });

            test('cast handles boolean true value', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, true, [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles boolean false value', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, false, [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles array value', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, ['Europe/London'], [], $context))
                    ->toThrow(ErrorException::class);
            });

            test('cast handles object value', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, new stdClass(), [], $context))
                    ->toThrow(Error::class);
            });

            test('cast handles float value', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, 123.45, [], $context))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles null value', function (): void {
                $cast = TimeZone::dataCastUsing();
                $property = mock(DataProperty::class);
                $context = mock(CreationContext::class);

                expect(fn (): mixed => $cast->cast($property, null, [], $context))
                    ->toThrow(MissingResourceException::class);
            });
        });
    });
});
