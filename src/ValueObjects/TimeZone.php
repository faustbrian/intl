<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\ValueObjects;

use Cline\Struct\AbstractData;
use Override;
use Stringable;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Timezones;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final readonly class TimeZone extends AbstractData implements Stringable
{
    public function __construct(
        public readonly string $value,
        public readonly string $localized,
    ) {}

    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @throws MissingResourceException
     */
    public static function createFromString(string $value): self
    {
        return new self(
            $value,
            Timezones::getName($value),
        );
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->toString();
    }

    public function toString(): string
    {
        return $this->value;
    }
}
