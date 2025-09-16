<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObject\Locale;
use Symfony\Component\Intl\Exception\MissingResourceException;

it('creates from valid locale string', function (): void {
    $validLocale = 'en_US';
    $locale = Locale::createFromString($validLocale);

    expect($locale->toString())->toEqual($validLocale);
});

it('throws exception for invalid locale string', function (): void {
    $invalidLocale = 'invalid-locale';
    Locale::createFromString($invalidLocale);
})->throws(MissingResourceException::class);

it('returns correct localized string representation', function (): void {
    $validLocale = 'en_US';
    $locale = Locale::createFromString($validLocale);

    expect($locale->localized)->toEqual('English (United States)');
});

it('returns correct string representation', function (): void {
    $validLocale = 'en_US';
    $locale = Locale::createFromString($validLocale);

    expect($locale->toString())->toEqual($validLocale);
});
