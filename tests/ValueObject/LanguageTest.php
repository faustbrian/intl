<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObject\Language;
use Symfony\Component\Intl\Exception\MissingResourceException;

it('creates from valid language code string', function (): void {
    $validLanguageCode = 'en';
    $languageCode = Language::createFromString($validLanguageCode);

    expect($languageCode->toString())->toEqual($validLanguageCode);
});

it('throws exception for invalid language code string', function (): void {
    $invalidLanguageCode = 'xx';
    Language::createFromString($invalidLanguageCode);
})->throws(MissingResourceException::class);

it('returns correct localized string representation', function (): void {
    $validLanguageCode = 'en';
    $languageCode = Language::createFromString($validLanguageCode);

    expect($languageCode->localized)->toEqual('English');
});

it('returns correct string representation', function (): void {
    $validLanguageCode = 'en';
    $languageCode = Language::createFromString($validLanguageCode);

    expect($languageCode->toString())->toEqual($validLanguageCode);
});
