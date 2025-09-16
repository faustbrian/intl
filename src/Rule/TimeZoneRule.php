<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Rule;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;
use Symfony\Component\Intl\Timezones;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class TimeZoneRule implements ValidationRule
{
    /**
     * {@inheritDoc}
     */
    #[Override()]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Timezones::exists((string) $value)) {
            $fail('The :attribute must be a valid timezone.');
        }
    }
}
