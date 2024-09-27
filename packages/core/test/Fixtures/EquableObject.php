<?php

declare(strict_types=1);

namespace ParTest\Core\Fixtures;

use Generator;
use Par\Core\Equable;

/**
 * @internal
 *
 * @template T of float|bool|int|string
 *
 * @implements Equable<T>
 */
final class EquableObject implements Equable
{
    /**
     * @param T $value
     */
    private function __construct(public readonly float|bool|int|string $value)
    {
    }

    /**
     * @return self<int>
     */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /**
     * @return Generator<self<int>>
     */
    public static function fromIntRange(int $start, int $end): Generator
    {
        yield from self::fromIterable(range($start, $end));
    }

    /**
     * @template TValue of float|bool|int|string
     *
     * @param iterable<TValue> $iterable
     *
     * @return Generator<self<TValue>>
     */
    public static function fromIterable(iterable $iterable): Generator
    {
        foreach ($iterable as $value) {
            yield new self($value);
        }
    }

    /**
     * @return self<string>
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * @return Generator<self<string>>
     */
    public static function fromStringRange(string $start, string $end): Generator
    {
        yield from self::fromIterable(range($start, $end));
    }

    public function equals(?Equable $other): bool
    {
        if ($other instanceof self) {
            return $other->value === $this->value;
        }

        return false;
    }
}
