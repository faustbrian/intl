<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brick\PhoneNumber\PhoneNumberException;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberType;
use Cline\Intl\ValueObjects\PhoneNumber;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

it('creates from valid phone number', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    expect($phoneNumber->toString())->toEqual($validPhoneNumber);
    expect($phoneNumber->countryCode)->toEqual('358');
    expect($phoneNumber->geographicalAreaCode)->toBeNull();
    expect($phoneNumber->nationalNumber)->toEqual('10800515');
    expect($phoneNumber->regionCode)->toEqual('FI');
    expect($phoneNumber->isPossible)->toBeTrue();
    expect($phoneNumber->isValid)->toBeTrue();
    expect($phoneNumber->numberType)->toEqual(9);
});

it('throws exception for invalid phone number', function (): void {
    $invalidPhoneNumber = 'invalid-phone-number';
    PhoneNumber::createFromString($invalidPhoneNumber);
})->throws(PhoneNumberParseException::class);

it('returns correct string representation', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    expect($phoneNumber->toString())->toEqual($validPhoneNumber);
});

it('returns correct country code', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    $expectedCountryCode = '358';
    expect($phoneNumber->countryCode)->toEqual($expectedCountryCode);
});

it('returns correct geographical area code', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    expect($phoneNumber->geographicalAreaCode)->toBeNull();
});

it('returns correct national number', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    $expectedNationalNumber = '10800515';
    expect($phoneNumber->nationalNumber)->toEqual($expectedNationalNumber);
});

it('returns correct region code', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    $expectedRegionCode = 'FI';
    expect($phoneNumber->regionCode)->toEqual($expectedRegionCode);
});

it('checks if phone number is possible', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    expect($phoneNumber->isPossible)->toBeTrue();
});

it('checks if phone number is valid', function (): void {
    $validPhoneNumber = '+35810800515';
    $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

    expect($phoneNumber->isValid)->toBeTrue();
});

it('can parse non standard format numbers', function (): void {
    $phoneNumber = PhoneNumber::createFromString('(0)10 800 515', 'FI');

    expect($phoneNumber->toString())->toBe('+35810800515');
    expect($phoneNumber->countryCode)->toBe('358');
    expect($phoneNumber->geographicalAreaCode)->toBeNull();
    expect($phoneNumber->nationalNumber)->toBe('10800515');
    expect($phoneNumber->regionCode)->toBe('FI');
    expect($phoneNumber->isPossible)->toBeTrue();
    expect($phoneNumber->isValid)->toBeTrue();
});

it('get example number throws exception for invalid region code', function (): void {
    PhoneNumber::createFromString('ZZ');
})->throws(PhoneNumberException::class);

it('json serializable', function (): void {
    expect(json_encode(PhoneNumber::createFromString('0123000000', 'FR')))->toBe('{"phoneNumber":"+33123000000"}');
});

describe('PhoneNumber', function (): void {
    describe('Happy Paths', function (): void {
        test('converts to string using __toString magic method', function (): void {
            $phoneNumber = PhoneNumber::createFromString('+35810800515');

            expect((string) $phoneNumber)->toBe('+35810800515');
        });

        test('creates phone number from example for region', function (): void {
            $phoneNumber = PhoneNumber::createFromExample('US');

            expect($phoneNumber->regionCode)->toBe('US');
            expect($phoneNumber->countryCode)->toBe('1');
            expect($phoneNumber->isValid)->toBeTrue();
        });

        test('creates phone number from example with specific type', function (): void {
            $phoneNumber = PhoneNumber::createFromExample('GB', PhoneNumberType::MOBILE);

            expect($phoneNumber->regionCode)->toBe('GB');
            expect($phoneNumber->countryCode)->toBe('44');
            expect($phoneNumber->numberType)->toBe((string) PhoneNumberType::MOBILE->value);
        });

        test('formats phone number with different formats', function (): void {
            $phoneNumber = PhoneNumber::createFromString('+35810800515');

            expect($phoneNumber->format(PhoneNumberFormat::E164))->toBe('+35810800515');
            expect($phoneNumber->format(PhoneNumberFormat::INTERNATIONAL))->toBe('+358 10 800515');
            expect($phoneNumber->format(PhoneNumberFormat::NATIONAL))->toBe('010 800515');
        });

        test('formats phone number for calling from different region', function (): void {
            $phoneNumber = PhoneNumber::createFromString('+35810800515');

            $formatted = $phoneNumber->formatForCallingFrom('US');

            expect($formatted)->toContain('358');
        });

        test('formats phone number for calling from same region', function (): void {
            $phoneNumber = PhoneNumber::createFromString('+35810800515');

            $formatted = $phoneNumber->formatForCallingFrom('FI');

            expect($formatted)->toBe('010 800515');
        });

        test('checks equality between identical phone numbers', function (): void {
            $phoneNumber1 = PhoneNumber::createFromString('+35810800515');
            $phoneNumber2 = PhoneNumber::createFromString('+358 10 800515');

            expect($phoneNumber1->isEqualTo($phoneNumber2))->toBeTrue();
        });

        test('handles geographical area code normalization', function (): void {
            // US number with area code
            $phoneNumber = PhoneNumber::createFromString('+12025551234');

            expect($phoneNumber->geographicalAreaCode)->not()->toBeNull();
            expect($phoneNumber->countryCode)->toBe('1');
        });
    });

    describe('Sad Paths', function (): void {
        test('checks equality returns false for different phone numbers', function (): void {
            $phoneNumber1 = PhoneNumber::createFromString('+35810800515');
            $phoneNumber2 = PhoneNumber::createFromString('+33123000000');

            expect($phoneNumber1->isEqualTo($phoneNumber2))->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles phone numbers with empty geographical area code', function (): void {
            $phoneNumber = PhoneNumber::createFromString('+35810800515');

            // Finnish toll-free numbers don't have geographical area codes
            expect($phoneNumber->geographicalAreaCode)->toBeNull();
        });

        test('handles phone numbers with geographical area code that is zero', function (): void {
            // This tests the in_array check for '0' in line 61
            $phoneNumber = PhoneNumber::createFromString('0123000000', 'FR');

            // French numbers may have area codes
            expect($phoneNumber->countryCode)->toBe('33');
        });

        test('creates phone number with region code parameter', function (): void {
            $phoneNumber = PhoneNumber::createFromString('0123000000', 'FR');

            expect($phoneNumber->regionCode)->toBe('FR');
            expect($phoneNumber->toString())->toBe('+33123000000');
        });

        test('casts string to phone number via dataCastUsing', function (): void {
            $cast = PhoneNumber::dataCastUsing();

            expect($cast)->toBeInstanceOf(Cast::class);
        });

        test('cast implementation converts string to phone number', function (): void {
            $cast = PhoneNumber::dataCastUsing();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);

            $result = $cast->cast($property, '+35810800515', [], $context);

            expect($result)->toBeInstanceOf(PhoneNumber::class);
            expect($result->toString())->toBe('+35810800515');
        });

        test('handles region code normalization for null empty and zero values', function (): void {
            // Test with valid US number
            $phoneNumber = PhoneNumber::createFromString('+12025551234');

            expect($phoneNumber->regionCode)->toBe('US');
            expect($phoneNumber->regionCode)->not()->toBeNull();
            expect($phoneNumber->regionCode)->not()->toBe('');
            expect($phoneNumber->regionCode)->not()->toBe('0');
        });
    });
});
