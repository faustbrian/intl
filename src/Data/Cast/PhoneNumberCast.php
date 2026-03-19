<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Data\Cast;

use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\PhoneNumber;
use Cline\Struct\Contracts\ContextualCastInterface;
use Cline\Struct\Metadata\PropertyMetadata;
use Cline\Struct\Support\PropertyHydrationContext;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class PhoneNumberCast implements ContextualCastInterface
{
    public function get(PropertyMetadata $property, mixed $value): ?PhoneNumber
    {
        return $this->castValue($value, []);
    }

    public function getWithContext(
        PropertyMetadata $property,
        mixed $value,
        PropertyHydrationContext $context,
    ): ?PhoneNumber {
        return $this->castValue($value, $context->resolvedProperties + $context->rawInput);
    }

    public function set(PropertyMetadata $property, mixed $value): mixed
    {
        return $value;
    }

    /**
     * @param array<string, mixed> $properties
     */
    private function castValue(mixed $value, array $properties): ?PhoneNumber
    {
        if (!is_string($value) || ($value === '' || $value === '0')) {
            return null;
        }

        $countryCode = $properties['countryCode'] ?? null;

        if ($countryCode instanceof Country) {
            $countryCode = $countryCode->toString();
        }

        /** @var null|string $countryCode */
        return PhoneNumber::createFromString($value, $countryCode);
    }
}
