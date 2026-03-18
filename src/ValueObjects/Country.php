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
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final readonly class Country extends AbstractData implements Stringable
{
    /**
     * @param string      $alpha2 the return value is an 'ISO 3166-1 alpha-2' compliant country code
     * @param null|string $alpha3 the return value is an 'ISO 3166-1 alpha-3' compliant country code
     */
    public function __construct(
        public readonly string $localized,
        public readonly string $alpha2,
        public readonly ?string $alpha3,
    ) {}

    #[Override()]
    public function __toString(): string
    {
        return $this->alpha2;
    }

    /**
     * @throws MissingResourceException
     */
    public static function createFromString(string $value): self
    {
        return new self(
            Countries::getName($value),
            $value,
            Countries::getAlpha3Code($value),
        );
    }

    public function isEqualTo(self $other): bool
    {
        return $this->alpha2 === $other->toString();
    }

    public function toString(): string
    {
        return $this->alpha2;
    }
}
