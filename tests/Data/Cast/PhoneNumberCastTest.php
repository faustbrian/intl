<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\ValueObject\PhoneNumber;
use Tests\Fakes\CastData;

it('can cast a country', function (): void {
    $actual = CastData::from([
        'countryCode' => 'FI',
        'phoneNumber' => '+358 40 1234567',
    ]);

    expect($actual->phoneNumber)->toBeInstanceOf(PhoneNumber::class);
});
