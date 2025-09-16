<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Fakes;

use Cline\Intl\Data\Cast\CountryCast;
use Cline\Intl\Data\Cast\CurrencyCast;
use Cline\Intl\Data\Cast\LanguageCast;
use Cline\Intl\Data\Cast\LocaleCast;
use Cline\Intl\Data\Cast\PhoneNumberCast;
use Cline\Intl\Data\Cast\PostalCodeCast;
use Cline\Intl\Data\Cast\PostalCodeStringCast;
use Cline\Intl\Data\Cast\TimeZoneCast;
use Cline\Intl\ValueObject\Country;
use Cline\Intl\ValueObject\Currency;
use Cline\Intl\ValueObject\Language;
use Cline\Intl\ValueObject\Locale;
use Cline\Intl\ValueObject\PhoneNumber;
use Cline\Intl\ValueObject\PostalCode;
use Cline\Intl\ValueObject\TimeZone;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class CastData extends Data
{
    public function __construct(
        #[WithCast(CountryCast::class)]
        public readonly ?Country $countryCode,
        #[WithCast(CurrencyCast::class)]
        public readonly ?Currency $currencyCode,
        #[WithCast(LanguageCast::class)]
        public readonly ?Language $languageCode,
        #[WithCast(LocaleCast::class)]
        public readonly ?Locale $localeCode,
        #[WithCast(PhoneNumberCast::class)]
        public readonly ?PhoneNumber $phoneNumber,
        #[WithCast(PostalCodeCast::class)]
        public readonly ?PostalCode $postalCode,
        #[WithCast(PostalCodeStringCast::class)]
        public readonly ?string $postalCodeString,
        #[WithCast(TimeZoneCast::class)]
        public readonly ?TimeZone $timeZone,
    ) {}
}
