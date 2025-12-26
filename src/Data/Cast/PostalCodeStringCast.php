<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Data\Cast;

use Brick\Postcode\InvalidPostcodeException;
use Brick\Postcode\UnknownCountryException;
use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\PostalCode;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;
use function throw_if;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class PostalCodeStringCast implements Cast
{
    /**
     * This will format the postal code according to the rules of the country
     * code. For example, for the country code 'LT' the postal code 'LT-12345'
     * will be formatted to '12345'. For the country code 'SE' the postal code
     * '12345' will be formatted to '123 45' and so on. If the postal code is
     * not valid for the country code, the original value will be returned.
     *
     * @phpstan-ignore-next-line missingType.generics (Cast returns string, not BaseData)
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?string
    {
        if (!is_string($value) || ($value === '' || $value === '0')) {
            return null;
        }

        $countryCode = $properties['countryCode'];

        if ($countryCode instanceof Country) {
            $countryCode = $countryCode->toString();
        }

        /** @var null|string $countryCode */
        try {
            $formattedPostalCode = PostalCode::createFromString($value, $countryCode)->toString();

            throw_if($formattedPostalCode === '' || $formattedPostalCode === '0', InvalidPostcodeException::class);

            return $formattedPostalCode;
        } catch (InvalidPostcodeException) {
            // If the postal code is invalid, return the original value.
        } catch (UnknownCountryException) {
            // If the country code is unknown, return the original value.
        }

        return $value;
    }
}
