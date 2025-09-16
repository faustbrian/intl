<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\ValueObject;

use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Stringable;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class Country extends Data implements Castable, Stringable
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

    #[Override()]
    public static function dataCastUsing(...$arguments): Cast
    {
        return new class() implements Cast
        {
            public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
            {
                return Country::createFromString((string) $value);
            }
        };
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
