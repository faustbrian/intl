<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Rules\LanguageRule;
use Illuminate\Support\Facades\Validator;

it('pass with known values', function (): void {
    expect(Validator::make(
        ['attribute' => 'fi'],
        ['attribute' => new LanguageRule()],
    )->passes())->toBeTrue();
});

it('fail with unknown values', function (): void {
    expect(Validator::make(
        ['attribute' => 'XX'],
        ['attribute' => new LanguageRule()],
    )->fails())->toBeTrue();
});
