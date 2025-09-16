<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Data\Cast;

use Cline\Intl\ValueObject\Locale;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class LocaleCast implements Cast
{
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?Locale
    {
        if (!is_string($value) || empty($value)) {
            return null;
        }

        return Locale::createFromString($value);
    }
}
