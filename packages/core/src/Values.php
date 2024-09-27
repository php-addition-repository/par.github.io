<?php

declare(strict_types=1);

namespace Par\Core;

use DateTime;
use DateTimeImmutable;

/**
 * Utility class with methods to assist in value handling.
 */
final class Values
{
    /**
     * Determines if two values should be considered equal.
     *
     * - If both values implement `\Par\Core\Equable` then `$value->equals($otherValue)` is used.
     * - When both values are instances of `\DateTime` or `\DateTimeImmutable` then `$value == $otherValue` is used.
     * - Otherwise a strict comparison (`$value === $otherValue`) is used.
     *
     * Usage:
     * ```php
     * if (Values::equals($a, $b)) {
     *     // $a and $b are equal
     * }
     * ```
     *
     * @template TValue
     *
     * @param mixed $value The value to test
     * @param TValue $otherValue The other value with which to compare
     *
     * @return bool `true` if both values should be considered equal
     *
     * @phpstan-assert-if-true TValue $value
     */
    public static function equals(mixed $value, mixed $otherValue): bool
    {
        if ($value === $otherValue) {
            return true;
        }

        if ($value instanceof Equable && $otherValue instanceof Equable) {
            return $value->equals($otherValue);
        }

        if ($value instanceof DateTimeImmutable && $otherValue instanceof DateTimeImmutable) {
            /* @phpstan-ignore equal.invalid (we explicitly use loose comparison) */
            return $value == $otherValue;
        }

        if ($value instanceof DateTime && $otherValue instanceof DateTime) {
            /* @phpstan-ignore equal.invalid (we explicitly use loose comparison) */
            return $value == $otherValue;
        }

        return false;
    }

    /**
     * Determines if a value should be considered equal to __any__ of the items in the list of other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsAnyIn($a, [$b, $c])) {
     *     // When equal to $b OR $c
     * }
     * ```
     *
     * @see Values::equals()
     *
     * @template TValue
     *
     * @param mixed $value The value to test
     * @param iterable<TValue> $otherValues The list of other values with which to compare
     *
     * @return bool `true` if value should be considered equal to any of the items in the list of other values
     *
     * @phpstan-assert-if-true TValue $value
     */
    public static function equalsAnyIn(mixed $value, iterable $otherValues): bool
    {
        return self::containsValue($value, $otherValues);
    }

    /**
     * Determines if a value should be considered equal to __any__ of the other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsAnyOf($a, $b, $c)) {
     *     // When equal to $b OR $c
     * }
     * ```
     *
     * @see Values::equals()
     *
     * @template TValue
     *
     * @param mixed $value The value to test
     * @param TValue ...$otherValues The other values with which to compare
     *
     * @return bool `true` if value should be considered equal to any of the other values
     *
     * @phpstan-assert-if-true TValue $value
     */
    public static function equalsAnyOf(mixed $value, mixed ...$otherValues): bool
    {
        return self::equalsAnyIn($value, $otherValues);
    }

    /**
     * Determines if a value should be considered equal to __none__ of the items in the list of other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsNoneIn($a, [$b, $c])) {
     *     // When not equal to $b AND $c
     * }
     * ```
     *
     * @see Values::equals()
     *
     * @template TValue
     *
     * @param mixed $value The value to test
     * @param iterable<TValue> $otherValues The list of other values with which to compare
     *
     * @return bool `true` if value should be considered equal to __none__ of the items in the list of other values
     *
     * @phpstan-assert-if-false TValue $value
     */
    public static function equalsNoneIn(mixed $value, iterable $otherValues): bool
    {
        return self::containsValue($value, $otherValues, false);
    }

    /**
     * Determines if a value should be considered equal to __none__ of the other values.
     *
     * Usage:
     * ```php
     * if (Values::equalsNoneOf($a, $b, $c)) {
     *     // When not equal to $b AND $c
     * }
     * ```
     *
     * @see Values::equals()
     *
     * @template TValue
     *
     * @param mixed $value The value to test
     * @param TValue ...$otherValues The other values with which to compare
     *
     * @return bool `true` if value should be considered equal to __none__ of the other values
     *
     * @phpstan-assert-if-false TValue $value
     */
    public static function equalsNoneOf(mixed $value, mixed ...$otherValues): bool
    {
        return self::equalsNoneIn($value, $otherValues);
    }

    /**
     * Generates a hash code for a sequence of input values.
     *
     * The hash code is generated as if all the input values were placed into an array, and that array were hashed by
     * calling `Values.hashCode([...])`.
     * This method is useful for implementing Equable.hashCode() on objects containing multiple fields. For example,
     * if an object that has three fields, x, y, and z, one could write:
     *
     * ```
     * public function hashCode(): int
     * {
     *     return Values.hash($this->x, $this->y, $this->z);
     * }
     * ```
     */
    public static function hash(mixed ...$values): int
    {
        return HashCode::forArray($values);
    }

    /**
     * Returns the hash code of a non-null argument and 0 for a null argument.
     *
     * @see HashCode::forAny()
     * @see Hashable
     *
     * @param mixed $value a value
     *
     * @return int the hash code of a non-null argument and 0 for a null argument
     */
    public static function hashCode(mixed $value): int
    {
        if ($value instanceof Hashable) {
            return $value->hashCode();
        }

        return HashCode::forAny($value);
    }

    /**
     * @template TValue
     *
     * @param iterable<TValue> $otherValues
     */
    private static function containsValue(mixed $value, iterable $otherValues, bool $onMatch = true): bool
    {
        foreach ($otherValues as $otherValue) {
            if (self::equals($value, $otherValue)) {
                return $onMatch;
            }
        }

        return !$onMatch;
    }
}
