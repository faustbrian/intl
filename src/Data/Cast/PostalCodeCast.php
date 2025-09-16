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
use Cline\Intl\ValueObject\Country;
use Cline\Intl\ValueObject\PostalCode;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class PostalCodeCast implements Cast
{
    /**
     * This will format the postal code according to the rules of the country
     * code. For example, for the country code 'LT' the postal code 'LT-12345'
     * will be formatted to '12345'. For the country code 'SE' the postal code
     * '12345' will be formatted to '123 45' and so on. If the postal code is
     * not valid for the country code, the original value will be returned.
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?PostalCode
    {
        if (!is_string($value) || empty($value)) {
            return null;
        }

        $countryCode = $properties['countryCode'];

        if ($countryCode instanceof Country) {
            $countryCode = $countryCode->toString();
        }

        try {
            return PostalCode::createFromString($value, $countryCode);
        } catch (InvalidPostcodeException) {
            // If the postal code is invalid, return the original value.
        } catch (UnknownCountryException) {
            // If the country code is unknown, return the original value.
        }

        return null;
    }
}
