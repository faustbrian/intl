<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;
use Symfony\Component\Intl\Languages;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class LanguageRule implements ValidationRule
{
    /**
     * {@inheritDoc}
     */
    #[Override()]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @phpstan-ignore-next-line cast.string */
        if (Languages::exists((string) $value)) {
            return;
        }

        $fail('The :attribute must be a valid language.');
    }
}
