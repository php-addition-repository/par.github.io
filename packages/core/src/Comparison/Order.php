<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Throwable;

/**
 * This enum represents the value of something when compared to another.
 */
enum Order: int
{
    case Lesser = -1;
    case Equal = 0;
    case Greater = 1;

    /**
     * Returns the inverted value of current `Par\Core\Comparison\Order`.
     */
    public function invert(): self
    {
        return match ($this) {
            self::Equal => $this,
            self::Lesser => self::Greater,
            self::Greater => self::Lesser,
        };
    }

    /**
     * @param callable(mixed):Throwable $throwableSupplier
     */
    public static function castOrThrow(mixed $value, callable $throwableSupplier): Order
    {
        if (is_int($value)) {
            return Order::from($value);
        }

        if (!$value instanceof Order) {
            throw $throwableSupplier($value);
        }

        return $value;
    }
}
