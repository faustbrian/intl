<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Data\Cast\TimeZoneCast;
use Cline\Intl\ValueObjects\TimeZone;
use Cline\Struct\Contracts\CastInterface;
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
                ->and($timezone->localized)->toBe('Hawaii-Aleutian Standard Time (Honolulu)');
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
            $data = CastData::create([
                'timeZone' => 'America/Chicago',
            ]);

            expect($data->timeZone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($data->timeZone->value)->toBe('America/Chicago');
        });

        test('casts Asia/Dubai timezone', function (): void {
            $data = CastData::create([
                'timeZone' => 'Asia/Dubai',
            ]);

            expect($data->timeZone)
                ->toBeInstanceOf(TimeZone::class)
                ->and($data->timeZone->value)->toBe('Asia/Dubai');
        });

        test('casts Europe/London timezone', function (): void {
            $data = CastData::create([
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
            expect(fn (): CastData => CastData::create([
                'timeZone' => 'Invalid/TimeZone',
            ]))->toThrow(MissingResourceException::class);
        });

        test('returns null for non-string value - integer', function (): void {
            $data = CastData::create([
                'timeZone' => 12_345,
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for non-string value - array', function (): void {
            $data = CastData::create([
                'timeZone' => ['Europe/London'],
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for non-string value - boolean', function (): void {
            $data = CastData::create([
                'timeZone' => true,
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for non-string value - object', function (): void {
            $data = CastData::create([
                'timeZone' => new stdClass(),
            ]);

            expect($data->timeZone)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty string', function (): void {
            $data = CastData::create([
                'timeZone' => '',
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null for string zero', function (): void {
            $data = CastData::create([
                'timeZone' => '0',
            ]);

            expect($data->timeZone)->toBeNull();
        });

        test('returns null when property is null', function (): void {
            $data = CastData::create([
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
            expect(CastData::create([
                'timeZone' => '   ',
            ])->timeZone)->toBeNull();
        });

        test('throws exception for timezone with leading whitespace', function (): void {
            expect(fn (): CastData => CastData::create([
                'timeZone' => ' Europe/London',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for timezone with trailing whitespace', function (): void {
            expect(fn (): CastData => CastData::create([
                'timeZone' => 'Europe/London ',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for special characters', function (): void {
            expect(fn (): CastData => CastData::create([
                'timeZone' => '@#$%',
            ]))->toThrow(MissingResourceException::class);
        });

        test('throws exception for very long string', function (): void {
            expect(fn (): CastData => CastData::create([
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
                $cast = new TimeZoneCast();

                expect($cast)->toBeInstanceOf(CastInterface::class);
            });

            test('cast converts valid timezone string to TimeZone', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $result = $cast->get($property, 'Europe/Helsinki');

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('Europe/Helsinki')
                    ->and($result->localized)->toBe('Eastern European Time (Helsinki)');
            });

            test('cast converts America/New_York timezone', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $result = $cast->get($property, 'America/New_York');

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('America/New_York');
            });

            test('cast converts Asia/Tokyo timezone', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $result = $cast->get($property, 'Asia/Tokyo');

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('Asia/Tokyo');
            });

            test('cast converts Pacific/Honolulu timezone', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $result = $cast->get($property, 'Pacific/Honolulu');

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('Pacific/Honolulu')
                    ->and($result->localized)->toBe('Hawaii-Aleutian Standard Time (Honolulu)');
            });

            test('cast handles integer by casting to string', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, 12_345))->toBeNull();
            });
        });

        describe('Sad Paths', function (): void {
            test('cast throws exception for invalid timezone string', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $cast->get($property, 'Invalid/TimeZone');
            })->throws(MissingResourceException::class);

            test('cast throws exception for non-existent timezone', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $cast->get($property, 'NotATimeZone');
            })->throws(MissingResourceException::class);

            test('cast throws exception for lowercase timezone', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $cast->get($property, 'europe/london');
            })->throws(MissingResourceException::class);

            test('cast throws exception for numeric string timezone', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $cast->get($property, '12345');
            })->throws(MissingResourceException::class);
        });

        describe('Edge Cases', function (): void {
            test('cast handles numeric string', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect(fn (): mixed => $cast->get($property, '999'))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles empty string', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, ''))->toBeNull();
            });

            test('cast handles whitespace-only string', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect(fn (): mixed => $cast->get($property, '   '))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles timezone with leading whitespace', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect(fn (): mixed => $cast->get($property, ' Europe/London'))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles timezone with trailing whitespace', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect(fn (): mixed => $cast->get($property, 'Europe/London '))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles special characters', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect(fn (): mixed => $cast->get($property, '@#$%'))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles very long string', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect(fn (): mixed => $cast->get($property, str_repeat('A', 100)))
                    ->toThrow(MissingResourceException::class);
            });

            test('cast handles timezone with underscore', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $result = $cast->get($property, 'America/Los_Angeles');

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('America/Los_Angeles');
            });

            test('cast handles timezone with multiple path segments', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                $result = $cast->get($property, 'America/Argentina/Buenos_Aires');

                expect($result)
                    ->toBeInstanceOf(TimeZone::class)
                    ->and($result->value)->toBe('America/Argentina/Buenos_Aires');
            });

            test('cast handles boolean true value', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, true))->toBeNull();
            });

            test('cast handles boolean false value', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, false))->toBeNull();
            });

            test('cast handles array value', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, ['Europe/London']))->toBeNull();
            });

            test('cast handles object value', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, new stdClass()))->toBeNull();
            });

            test('cast handles float value', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, 123.45))->toBeNull();
            });

            test('cast handles null value', function (): void {
                $cast = new TimeZoneCast();
                $property = dummyPropertyMetadata();

                expect($cast->get($property, null))->toBeNull();
            });
        });
    });
});
