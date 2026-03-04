<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Data\Cast;

use Cline\Intl\ValueObjects\Country;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class CountryCast implements Cast
{
    /**
     * @param CreationContext<Country> $context
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?Country
    {
        if (!is_string($value) || ($value === '' || $value === '0')) {
            return null;
        }

        return Country::createFromString($value);
    }
}
