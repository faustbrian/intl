<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObject\Currency;
use Symfony\Component\Intl\Exception\MissingResourceException;

it('creates from valid currency code string', function (): void {
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

it('throws exception for invalid currency code string', function (): void {
    Currency::createFromString('XXX');
})->throws(MissingResourceException::class);
