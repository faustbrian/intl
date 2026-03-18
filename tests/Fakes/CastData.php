<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Fakes;

use Cline\Struct\AbstractData;
use Cline\Struct\Attributes\CastWith;
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

/**
 * @author Brian Faust <brian@cline.sh>
 */
final readonly class CastData extends AbstractData
{
    public function __construct(
        #[CastWith(CountryCast::class)]
        public readonly ?Country $countryCode,
        #[CastWith(CurrencyCast::class)]
        public readonly ?Currency $currencyCode,
        #[CastWith(LanguageCast::class)]
        public readonly ?Language $languageCode,
        #[CastWith(LocaleCast::class)]
        public readonly ?Locale $localeCode,
        #[CastWith(PhoneNumberCast::class)]
        public readonly ?PhoneNumber $phoneNumber,
        #[CastWith(PostalCodeCast::class)]
        public readonly ?PostalCode $postalCode,
        #[CastWith(PostalCodeStringCast::class)]
        public readonly ?string $postalCodeString,
        #[CastWith(TimeZoneCast::class)]
        public readonly ?TimeZone $timeZone,
    ) {}
}
