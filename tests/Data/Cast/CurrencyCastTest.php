<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObject\Currency;
use Tests\Fakes\CastData;

it('can cast a currency', function (): void {
    $actual = CastData::from([
        'currencyCode' => 'EUR',
    ]);

    expect($actual->currencyCode)->toBeInstanceOf(Currency::class);
});
