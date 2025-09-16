<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Rule\PhoneNumberRule;
use Illuminate\Support\Facades\Validator;

it('pass with known values', function (): void {
    expect(Validator::make(
        ['attribute' => '+33123456789'],
        ['attribute' => new PhoneNumberRule()],
    )->passes())->toBeTrue();

    expect(Validator::make(
        ['attribute' => '01 23 45 67 89'],
        ['attribute' => new PhoneNumberRule(regionCode: 'FR')],
    )->passes())->toBeTrue();

    expect(Validator::make(
        ['attribute' => '01 23 45 67 89', 'region' => ['code' => 'FR']],
        ['attribute' => new PhoneNumberRule(regionCodeReference: 'region.code')],
    )->passes())->toBeTrue();
});

it('fail with unknown values', function (): void {
    expect(Validator::make(
        ['attribute' => 'XX'],
        ['attribute' => new PhoneNumberRule()],
    )->fails())->toBeTrue();
});
