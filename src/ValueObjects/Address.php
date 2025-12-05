<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\ValueObjects;

use Cline\Intl\Enums\CountryCode;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Data;

/**
 * Field names are based to the OASIS "eXtensible Address Language" (xAL)
 * standard. The xAL standard is a data interchange format for addresses.
 *
 * The sole exception is the "phoneNumbers" field, which is not a component of
 * the xAL standard. This field has been incorporated due to the frequent
 * inclusion of phone numbers in addresses, particularly for shipping purposes.
 *
 * @author Brian Faust <brian@cline.sh>
 * @see https://en.wikipedia.org/wiki/VCard
 * @see http://www.oasis-open.org/committees/ciq/download.shtml
 */
final class Address extends Data
{
    /**
     * These properties are ordered from general to specific. This order starts
     * with the country and ends with the person's or organization's name, if
     * applicable.
     *
     * @param CountryCode $countryCode        A CLDR country code. CLDR codes
     *                                        include additional regions for addressing purposes, such as the Canary
     *                                        Islands (IC).
     * @param null|string $administrativeArea The broader administrative region
     *                                        of the address. Known as "state" in the United States, "region" in
     *                                        France, "province" in Italy, "county" in Great Britain, "prefecture" in
     *                                        Japan, etc.
     * @param string      $locality           The city or town part of the
     *                                        address. For instance, in the United States, this refers to the city,
     *                                        town, village, or any municipality excluding the state, province, county,
     *                                        or other administrative divisions.
     * @param null|string $dependentLocality  For addresses in Great Britain
     *                                        with a double-dependent locality, this includes both the double-dependent
     *                                        locality and the dependent locality. For example, "Whaley, Langwith."
     * @param string      $addressLine1       Typically used for the primary
     *                                        address information, usually including street name and number, and, if
     *                                        applicable, an apartment or suite number. E.g., "123 Main Street,
     *                                        Apartment 4."
     * @param null|string $addressLine2       Often used for additional address
     *                                        details not suitable for the first line. This might include apartment,
     *                                        suite, or unit numbers, or in business addresses, a department or a
     *                                        specific individual's name. E.g., "Building B, Floor 3" or "Attention:
     *                                        John Doe."
     * @param null|string $addressLine3       Less common in many countries, but
     *                                        vital for addresses in large complexes or regions with complex addressing
     *                                        systems. It may include specific delivery instructions or additional
     *                                        location details like a mail stop code in large institutions or a
     *                                        specific area within a university campus.
     * @param ?string     $fullName           the full name of the person,
     *                                        including prefix and suffix, if applicable (e.g., "Dr. John Doe Jr.")
     * @param ?string     $givenName          the given name of the person. Also
     *                                        known as the first name or forename of the person
     * @param ?string     $additionalName     the additional name of the person.
     *                                        This is used for middle names, initials, and other secondary names of the
     *                                        person
     * @param ?string     $familyName         the family name of the person.
     *                                        Also known as the last name or surname of the person
     */
    public function __construct(
        public readonly ?CountryCode $countryCode,
        public readonly ?string $administrativeArea,
        public readonly string $locality,
        public readonly ?string $dependentLocality,
        public readonly ?string $postalCode,
        public readonly ?string $sortingCode,
        public readonly ?string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly ?string $addressLine3,
        public readonly ?string $fullName,
        public readonly ?string $givenName,
        public readonly ?string $additionalName,
        public readonly ?string $familyName,
        public readonly ?string $organization,
        public readonly ?string $locale,
        public readonly ?string $phoneNumber,
        #[RequiredIf('longitude')]
        public readonly ?float $latitude,
        #[RequiredIf('latitude')]
        public readonly ?float $longitude,
    ) {}

    public function isPrivateAddress(): bool
    {
        return $this->organization === null;
    }

    public function isCompanyAddress(): bool
    {
        return $this->organization !== null;
    }

    public function isEqualTo(self $other): bool
    {
        return
            $this->countryCode === $other->countryCode
            && $this->administrativeArea === $other->administrativeArea
            && $this->locality === $other->locality
            && $this->dependentLocality === $other->dependentLocality
            && $this->postalCode === $other->postalCode
            && $this->sortingCode === $other->sortingCode
            && $this->addressLine1 === $other->addressLine1
            && $this->addressLine2 === $other->addressLine2
            && $this->addressLine3 === $other->addressLine3
            && $this->fullName === $other->fullName
            && $this->givenName === $other->givenName
            && $this->additionalName === $other->additionalName
            && $this->familyName === $other->familyName
            && $this->organization === $other->organization
            && $this->locale === $other->locale
            && $this->phoneNumber === $other->phoneNumber
            && $this->latitude === $other->latitude
            && $this->longitude === $other->longitude;
    }
}
