<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Enums\CountryCode;
use Cline\Intl\ValueObjects\Address;

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

describe('Address Equality', function (): void {
    describe('Happy Paths', function (): void {
        test('equals with identical addresses', function (): void {
            $address1 = createSampleAddress();
            $address2 = createSampleAddress();

            expect($address1->isEqualTo($address2))->toBeTrue();
        });

        test('equals with identical addresses including all fields', function (): void {
            $address1 = createCompleteAddress();
            $address2 = createCompleteAddress();

            expect($address1->isEqualTo($address2))->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        test('not equals when country code differs', function (): void {
            $address1 = createSampleAddress();
            $address2 = Address::from([
                'countryCode' => CountryCode::GB,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'addressLine2' => 'Apt 4',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
            ]);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });

        test('not equals when locality differs', function (): void {
            $address1 = createSampleAddress();
            $address2 = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'addressLine2' => 'Apt 4',
                'locality' => 'Different City',
                'administrativeArea' => 'NY',
            ]);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });

        test('not equals when postal code differs', function (): void {
            $address1 = createSampleAddress();
            $address2 = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '54321',
                'addressLine1' => '123 Main St',
                'addressLine2' => 'Apt 4',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
            ]);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });

        test('not equals when organization differs', function (): void {
            $address1 = createSampleAddress();
            $address2 = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'addressLine2' => 'Apt 4',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'organization' => 'Acme Corp',
            ]);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });

        test('not equals when coordinates differ', function (): void {
            $address1 = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'latitude' => 40.712_8,
                'longitude' => -74.006_0,
            ]);
            $address2 = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'latitude' => 51.507_4,
                'longitude' => -0.127_8,
            ]);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });
    });
});

describe('Address Type Detection', function (): void {
    describe('Happy Paths', function (): void {
        test('identifies private address when organization is null', function (): void {
            $address = createSampleAddress();

            expect($address->isPrivateAddress())->toBeTrue();
            expect($address->isCompanyAddress())->toBeFalse();
        });

        test('identifies company address when organization is set', function (): void {
            $address = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'organization' => 'Acme Corporation',
            ]);

            expect($address->isCompanyAddress())->toBeTrue();
            expect($address->isPrivateAddress())->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('identifies private address with empty string organization as company', function (): void {
            $address = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'organization' => '',
            ]);

            expect($address->isCompanyAddress())->toBeTrue();
            expect($address->isPrivateAddress())->toBeFalse();
        });
    });
});

describe('Coordinate Validation', function (): void {
    describe('Happy Paths', function (): void {
        test('creates address with both latitude and longitude', function (): void {
            $address = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'latitude' => 40.712_8,
                'longitude' => -74.006_0,
            ]);

            expect($address->latitude)->toBe(40.712_8);
            expect($address->longitude)->toBe(-74.006_0);
        });

        test('creates address without coordinates', function (): void {
            $address = createSampleAddress();

            expect($address->latitude)->toBeNull();
            expect($address->longitude)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles zero coordinates', function (): void {
            $address = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'latitude' => 0.0,
                'longitude' => 0.0,
            ]);

            expect($address->latitude)->toBe(0.0);
            expect($address->longitude)->toBe(0.0);
        });

        test('handles extreme latitude values', function (): void {
            $address = Address::from([
                'countryCode' => CountryCode::US,
                'postalCode' => '12345',
                'addressLine1' => '123 Main St',
                'locality' => 'Anytown',
                'administrativeArea' => 'NY',
                'latitude' => -90.0,
                'longitude' => 180.0,
            ]);

            expect($address->latitude)->toBe(-90.0);
            expect($address->longitude)->toBe(180.0);
        });
    });
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

function createCompleteAddress(): Address
{
    return Address::from([
        'countryCode' => CountryCode::US,
        'administrativeArea' => 'NY',
        'locality' => 'Anytown',
        'dependentLocality' => 'District 5',
        'postalCode' => '12345',
        'sortingCode' => 'ABC123',
        'addressLine1' => '123 Main St',
        'addressLine2' => 'Apt 4',
        'addressLine3' => 'Building B',
        'fullName' => 'Dr. John Doe Jr.',
        'givenName' => 'John',
        'additionalName' => 'Michael',
        'familyName' => 'Doe',
        'organization' => 'Acme Corp',
        'locale' => 'en_US',
        'phoneNumber' => '+1-555-0123',
        'latitude' => 40.712_8,
        'longitude' => -74.006_0,
    ]);
}
