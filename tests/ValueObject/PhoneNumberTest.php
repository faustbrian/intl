<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brick\PhoneNumber\PhoneNumberException;
use Brick\PhoneNumber\PhoneNumberParseException;
use Cline\Intl\ValueObject\PhoneNumber;

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
