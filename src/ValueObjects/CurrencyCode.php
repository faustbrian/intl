<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\ValueObjects;

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
final class CurrencyCode extends Data implements Castable, Stringable
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
            Currencies::getName($value),
        );
    }

    /**
     * @param array<mixed> $arguments
     */
    #[Override()]
    public static function dataCastUsing(...$arguments): Cast
    {
        return new class() implements Cast
        {
            /**
             * @phpstan-ignore-next-line missingType.generics
             */
            public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
            {
                /** @phpstan-ignore-next-line cast.string */
                return CurrencyCode::createFromString((string) $value);
            }
        };
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
