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
use Cline\Struct\AbstractData;
use Cline\Struct\Attributes\CastWith;

/**
 * @author Brian Faust <brian@cline.sh>
 * @psalm-immutable
 */
final readonly class CastData extends AbstractData
{
    public function __construct(
        #[CastWith(CountryCast::class)]
        public ?Country $countryCode,
        #[CastWith(CurrencyCast::class)]
        public ?Currency $currencyCode,
        #[CastWith(LanguageCast::class)]
        public ?Language $languageCode,
        #[CastWith(LocaleCast::class)]
        public ?Locale $localeCode,
        #[CastWith(PhoneNumberCast::class)]
        public ?PhoneNumber $phoneNumber,
        #[CastWith(PostalCodeCast::class)]
        public ?PostalCode $postalCode,
        #[CastWith(PostalCodeStringCast::class)]
        public ?string $postalCodeString,
        #[CastWith(TimeZoneCast::class)]
        public ?TimeZone $timeZone,
    ) {}
}
