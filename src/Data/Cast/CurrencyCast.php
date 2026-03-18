<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Data\Cast;

use Cline\Struct\Contracts\CastInterface;
use Cline\Struct\Metadata\PropertyMetadata;
use Cline\Intl\ValueObjects\Currency;
use Stringable;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class CurrencyCast implements CastInterface
{
    public function get(PropertyMetadata $property, mixed $value): ?Currency
    {
        if ($value instanceof Currency) {
            return $value;
        }

        if ($value instanceof Stringable) {
            $value = $value->__toString();
        }

        if (!is_string($value) || ($value === '' || $value === '0')) {
            return null;
        }

        return Currency::createFromString($value);
    }

    public function set(PropertyMetadata $property, mixed $value): mixed
    {
        return $value;
    }
}
