<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Enum\CountryCode;
use Cline\Intl\ValueObject\Address;

it('constructor and to string', function (): void {
    $address = createSampleAddress();

    expect($address->countryCode)->toEqual(CountryCode::US);
    expect($address->administrativeArea)->toEqual('NY');
    expect($address->locality)->toEqual('Anytown');
    expect($address->dependentLocality)->toBeNull();
    expect($address->postalCode)->toEqual('12345');
    expect($address->sortingCode)->toBeNull();
    expect($address->addressLine1)->toEqual('123 Main St');
    expect($address->addressLine2)->toEqual('Apt 4');
    expect($address->addressLine3)->toBeNull();
    expect($address->fullName)->toBeNull();
    expect($address->givenName)->toBeNull();
    expect($address->additionalName)->toBeNull();
    expect($address->familyName)->toBeNull();
    expect($address->organization)->toBeNull();
    expect($address->locale)->toBeNull();
});

it('equals with identical addresses', function (): void {
    $address1 = createSampleAddress();
    $address2 = createSampleAddress();

    expect($address1->isEqualTo($address2))->toBeTrue();
});
function createSampleAddress(): Address
{
    return Address::from([
        'countryCode' => CountryCode::US,
        'postalCode' => '12345',
        'addressLine1' => '123 Main St',
        'addressLine2' => 'Apt 4',
        'locality' => 'Anytown',
        'administrativeArea' => 'NY',
    ]);
}
