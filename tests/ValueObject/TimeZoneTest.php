<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObject\TimeZone;
use Symfony\Component\Intl\Exception\MissingResourceException;

it('creates from valid timezone string', function (): void {
    $validTimeZone = 'Europe/Helsinki';
    $timeZone = TimeZone::createFromString($validTimeZone);

    expect($timeZone->toString())->toEqual($validTimeZone);
});

it('throws exception for invalid timezone string', function (): void {
    $invalidTimeZone = 'Invalid/TimeZone';

    TimeZone::createFromString($invalidTimeZone);
})->throws(MissingResourceException::class);

it('returns correct localized string representation', function (): void {
    $validTimeZone = 'Europe/Helsinki';
    $timeZone = TimeZone::createFromString($validTimeZone);

    expect($timeZone->localized)->toEqual('Eastern European Time (Helsinki)');
});

it('returns correct string representation', function (): void {
    $validTimeZone = 'Europe/Helsinki';
    $timeZone = TimeZone::createFromString($validTimeZone);

    expect($timeZone->toString())->toEqual($validTimeZone);
});
