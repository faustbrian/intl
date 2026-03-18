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
use Cline\Struct\Contracts\ContextualCastInterface;
use Cline\Struct\Metadata\PropertyMetadata;
use Cline\Struct\Support\PropertyHydrationContext;
use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\PostalCode;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class PostalCodeCast implements ContextualCastInterface
{
    public function get(PropertyMetadata $property, mixed $value): ?PostalCode
    {
        return $this->castValue($value, []);
    }

    public function getWithContext(
        PropertyMetadata $property,
        mixed $value,
        PropertyHydrationContext $context,
    ): ?PostalCode {
        return $this->castValue($value, $context->resolvedProperties + $context->rawInput);
    }

    public function set(PropertyMetadata $property, mixed $value): mixed
    {
        return $value;
    }

    /**
     * This will format the postal code according to the rules of the country
     * code. For example, for the country code 'LT' the postal code 'LT-12345'
     * will be formatted to '12345'. For the country code 'SE' the postal code
     * '12345' will be formatted to '123 45' and so on. If the postal code is
     * not valid for the country code, the original value will be returned.
     *
     * @param array<string, mixed> $properties
     */
    private function castValue(mixed $value, array $properties): ?PostalCode
    {
        if (!is_string($value) || ($value === '' || $value === '0')) {
            return null;
        }

        $countryCode = $properties['countryCode'] ?? null;

        if ($countryCode instanceof Country) {
            $countryCode = $countryCode->toString();
        }

        /** @var null|string $countryCode */
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
