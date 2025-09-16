<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Formatter;

use Cline\Intl\ValueObject\Address;
use CommerceGuys\Addressing\Address as PostalAddress;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Formatter\PostalLabelFormatter as Formatter;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use InvalidArgumentException;

use function is_string;
use function mb_strtolower;
use function mb_strtoupper;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class PostalLabelFormatter
{
    public static function format(Address $sender, Address $recipient): string
    {
        if (!$sender->countryCode instanceof \Cline\Intl\Enum\CountryCode) {
            throw new InvalidArgumentException('The sender country code is required');
        }

        if (!$recipient->countryCode instanceof \Cline\Intl\Enum\CountryCode) {
            throw new InvalidArgumentException('The recipient country code is required');
        }

        $address = new PostalAddress();

        $address = $address->withCountryCode($recipient->countryCode->value);

        if (is_string($recipient->administrativeArea)) {
            $address = $address->withAdministrativeArea($recipient->administrativeArea);
        }

        /** @var PostalAddress $address */
        $address = $address->withLocality($recipient->locality);

        if (is_string($recipient->dependentLocality)) {
            $address = $address->withDependentLocality($recipient->dependentLocality);
        }

        if (is_string($recipient->postalCode)) {
            $address = $address->withPostalCode($recipient->postalCode);
        }

        if (is_string($recipient->sortingCode)) {
            $address = $address->withSortingCode($recipient->sortingCode);
        }

        if (is_string($recipient->addressLine1)) {
            $address = $address->withAddressLine1($recipient->addressLine1);
        }

        if (is_string($recipient->addressLine2)) {
            $address = $address->withAddressLine2($recipient->addressLine2);
        }

        if (is_string($recipient->addressLine3)) {
            $address = $address->withAddressLine3($recipient->addressLine3);
        }

        if (is_string($recipient->organization)) {
            $address = $address->withOrganization($recipient->organization);
        }

        if (is_string($recipient->givenName)) {
            $address = $address->withGivenName($recipient->givenName);
        }

        if (is_string($recipient->additionalName)) {
            $address = $address->withAdditionalName($recipient->additionalName);
        }

        if (is_string($recipient->familyName)) {
            $address = $address->withFamilyName($recipient->familyName);
        }

        if (is_string($recipient->locale)) {
            $address = $address->withLocale($recipient->locale);
        }

        return
            new Formatter(
                new AddressFormatRepository(),
                new CountryRepository(),
                new SubdivisionRepository(),
                ['locale' => mb_strtolower($sender->countryCode->value)],
            )
                ->format($address, [
                    'origin_country' => mb_strtoupper($sender->countryCode->value),
                ]);
    }
}
