<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tests\Fakes\CastData;

it('can cast a postal code', function (): void {
    $actual = CastData::from([
        'countryCode' => 'SE',
        'postalCodeString' => '12345',
    ]);

    expect($actual->postalCodeString)->toBe('123 45');
});
