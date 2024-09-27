<?php

declare(strict_types=1);

namespace Par\Core;

use TypeError;

/**
 * Internal utility class with methods to easily transform a value of any type to an integer representation.
 *
 * @internal
 */
final class HashCode
{
    private const MAX_ARRAY_RECURSION = 10;

    /**
     * Transform any value to a hash.
     *
     * @param mixed $value The value to transform to a hash
     *
     * @return int The resulting hash
     */
    public static function forAny(mixed $value): int
    {
        return self::recursiveForAny($value);
    }

    /**
     * Transform an array to a hash.
     *
     * @param mixed[] $value The array to transform
     *
     * @return int The resulting hash
     */
    public static function forArray(array $value): int
    {
        return self::recursiveForArray($value);
    }

    /**
     * Transform a boolean to integer hash.
     *
     * @param bool $value The boolean to transform
     *
     * @return int The resulting hash
     */
    public static function forBool(bool $value): int
    {
        return $value ? 1231 : 1237;
    }

    /**
     * Transform a float to integer hash.
     *
     * @param float $value The float to transform
     *
     * @return int The resulting hash
     */
    public static function forFloat(float $value): int
    {
        $int = unpack('i', pack('f', $value));

        return self::handleOverflow($int[1]);
    }

    /**
     * Transform an integer to integer hash.
     *
     * @param int $value The integer to transform
     *
     * @return int The resulting hash
     */
    public static function forInt(int $value): int
    {
        return $value;
    }

    /**
     * Transform an object to integer hash.
     *
     * @param object $value The object to transform
     *
     * @return int The resulting hash
     */
    public static function forObject(object $value): int
    {
        return self::handleOverflow(spl_object_id($value));
    }

    /**
     * Transform an resource to integer hash.
     *
     * @param resource $value The resource to transform
     *
     * @return int The resulting hash
     */
    public static function forResource(mixed $value): int
    {
        // PHP does not (yet) support an argument type for resource AND handles closed resource differently.
        $typeName = gettype($value);
        if (!in_array($typeName, ['resource', 'resource (closed)'], true)) {
            throw new TypeError(
                sprintf(
                    'Argument 1 passed to %s() must be of type resource, %s given',
                    __FUNCTION__,
                    $typeName
                )
            );
        }

        return self::handleOverflow((int) $value);
    }

    /**
     * Transform a string to a hash.
     *
     * @param string $value The string to transform
     *
     * @return int The resulting hash
     */
    public static function forString(string $value): int
    {
        $hash = 0;
        $len = mb_strlen($value, 'UTF-8');
        for ($i = 0; $i < $len; ++$i) {
            $char = mb_substr($value, $i, 1, 'UTF-8');
            $unicodeDecimalPosition = unpack('V', iconv('UTF-8', 'UCS-4LE', $char))[1];
            $hash = self::handleOverflow(31 * $hash + $unicodeDecimalPosition);
        }

        return $hash;
    }

    /**
     * Handles overflowing of an integer beyond 32 bit integer.
     *
     * @return int 32 bit integer
     */
    private static function handleOverflow(int $value): int
    {
        $bits = 32;
        $sign_mask = 1 << $bits - 1;
        $clamp_mask = ($sign_mask << 1) - 1;

        if ($value & $sign_mask) {
            return ((~$value & $clamp_mask) + 1) * -1;
        }

        return $value & $clamp_mask;
    }

    private static function recursiveForAny(mixed $value, int $maxRecursion = self::MAX_ARRAY_RECURSION): int
    {
        $type = gettype($value);

        return match ($type) {
            'boolean' => self::forBool($value),
            'integer' => self::forInt($value),
            'double' => self::forFloat($value),
            'string' => self::forString($value),
            'array' => self::recursiveForArray($value, $maxRecursion),
            'object' => self::forObject($value),
            'resource', 'resource (closed)' => self::forResource($value),
            'NULL' => 0,
            default => throw new TypeError(
                sprintf(
                    'Argument 1 passed to %s() must a value of a supported type, %s given',
                    'forAny',
                    $type
                )
            ),
        };
    }

    /**
     * @param mixed[] $value
     */
    private static function recursiveForArray(array $value, int $maxRecursion = self::MAX_ARRAY_RECURSION): int
    {
        if (0 === $maxRecursion || empty($value)) {
            return 0;
        }

        // Generate list a hashes for all values
        $hashes = array_map(
            static function($item) use ($maxRecursion) {
                return self::recursiveForAny($item, $maxRecursion - 1);
            },
            $value
        );

        // Add a single hash for all keys if the array is a map
        if (!array_is_list($value)) {
            $hashes[] = self::recursiveForArray(array_keys($value), 1);
        }

        return array_reduce(
            $hashes,
            static function(int $previous, int $hash): int {
                return self::handleOverflow($previous + $hash);
            },
            0
        );
    }
}
