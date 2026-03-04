<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Rules;

use Brick\PhoneNumber\PhoneNumberParseException;
use Cline\Intl\ValueObjects\PhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class PhoneNumberRule implements DataAwareRule, ValidationRule
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(
        private readonly ?string $regionCode = null,
        private readonly ?string $regionCodeReference = null,
        private readonly bool $shouldBeStrict = false,
    ) {}

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $data
     */
    #[Override()]
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    #[Override()]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            if ($this->regionCodeReference !== null) {
                $regionCode = Arr::get($this->data, $this->regionCodeReference);
            } else {
                $regionCode = $this->regionCode;
            }

            if ($this->shouldBeStrict) {
                /** @phpstan-ignore-next-line cast.string */
                $isValid = PhoneNumber::createFromString((string) $value, (string) $regionCode)->isValid;
            } else {
                /** @phpstan-ignore-next-line cast.string */
                $isValid = PhoneNumber::createFromString((string) $value, (string) $regionCode)->isPossible;
            }

            if ($isValid) {
                return;
            }
        } catch (PhoneNumberParseException) {
            //
        }

        $fail('The :attribute is not a valid phone number.');
    }
}
