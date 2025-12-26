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
use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\Currency;
use Cline\Intl\ValueObjects\Language;
use Cline\Intl\ValueObjects\Locale;
use Cline\Intl\ValueObjects\PhoneNumber;
use Cline\Intl\ValueObjects\PostalCode;
use Cline\Intl\ValueObjects\TimeZone;
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
