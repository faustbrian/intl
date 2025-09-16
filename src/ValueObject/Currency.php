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
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class Currency extends Data implements Castable, Stringable
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $symbol,
        public readonly int $fractionDigits,
        public readonly int $roundingIncrement,
        public readonly int $cashFractionDigits,
        public readonly int $cashRoundingIncrement,
        public readonly ?int $numericCode,
    ) {}

    #[Override()]
    public function __toString(): string
    {
        return $this->code;
    }

    /**
     * @throws MissingResourceException
     */
    public static function createFromString(string $value): self
    {
        try {
            $numericCode = Currencies::getNumericCode($value);
        } catch (MissingResourceException) {
            $numericCode = null;
        }

        return new self(
            $value,
            Currencies::getName($value),
            Currencies::getSymbol($value),
            Currencies::getFractionDigits($value),
            Currencies::getRoundingIncrement($value),
            Currencies::getCashFractionDigits($value),
            Currencies::getCashRoundingIncrement($value),
            $numericCode,
        );
    }

    #[Override()]
    public static function dataCastUsing(...$arguments): Cast
    {
        return new class() implements Cast
        {
            public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
            {
                return Currency::createFromString((string) $value);
            }
        };
    }

    public function isEqualTo(self $other): bool
    {
        return $this->code === $other->toString();
    }

    public function toString(): string
    {
        return $this->code;
    }
}
