<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObject\Country;
use Symfony\Component\Intl\Exception\MissingResourceException;

it('creates from valid country code string', function (): void {
    $validCountryCode = 'US';
    $countryCode = Country::createFromString($validCountryCode);

    expect($countryCode->alpha2)->toEqual('US');
    expect($countryCode->alpha3)->toEqual('USA');
    expect($countryCode->toString())->toEqual($validCountryCode);
});

it('throws exception for invalid country code string', function (): void {
    $invalidCountryCode = 'XX';

    Country::createFromString($invalidCountryCode);
})->throws(MissingResourceException::class);

it('returns correct localized string representation', function (): void {
    $validCountryCode = 'US';
    $countryCode = Country::createFromString($validCountryCode);

    expect($countryCode->localized)->toEqual('United States');
});

it('returns correct string representation', function (): void {
    $validCountryCode = 'US';
    $countryCode = Country::createFromString($validCountryCode);

    expect($countryCode->toString())->toEqual($validCountryCode);
});
