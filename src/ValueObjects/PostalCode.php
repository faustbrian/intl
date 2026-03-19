<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\ValueObjects;

use Brick\Postcode\InvalidPostcodeException;
use Brick\Postcode\PostcodeFormatter;
use Brick\Postcode\UnknownCountryException;
use Cline\Struct\AbstractData;
use Override;
use Stringable;

/**
 * @author Brian Faust <brian@cline.sh>
 * @psalm-immutable
 */
final readonly class PostalCode extends AbstractData implements Stringable
{
    public function __construct(
        public string $postalCode,
    ) {}

    #[Override()]
    public function __toString(): string
    {
        return $this->postalCode;
    }

    /**
     * @throws InvalidPostcodeException
     * @throws UnknownCountryException
     */
    public static function createFromString(string $postalCode, ?string $countryCode): self
    {
        if ($countryCode === null) {
            return new self($postalCode);
        }

        return new self(
            new PostcodeFormatter()->format($countryCode, $postalCode),
        );
    }

    public function isEqualTo(self $other): bool
    {
        return $this->postalCode === $other->toString();
    }

    public function toString(): string
    {
        return $this->postalCode;
    }
}
