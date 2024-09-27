<?php

declare(strict_types=1);

namespace ParTest\Core\Unit;

use Par\Core\HashCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

/**
 * @internal
 */
final class HashCodeTest extends TestCase
{
    public static function anyProvider(): iterable
    {
        yield 'null' => [null, 0];
        yield from self::boolProvider();
        yield from self::intProvider();
        yield from self::floatProvider();
        yield from self::stringProvider();
        yield from self::arrayProvider();
        yield from self::objectProvider();
        yield from self::resourceProvider();
    }

    public static function arrayProvider(): iterable
    {
        yield 'array-list' => [[1, 4], 5];
        yield 'array-map' => [[1 => 2, 3 => 4], 10];
        yield 'array-max-recursion' => [[1, [1, [1, [1, [1, [1, [1, [1, [1, [1, [1, [1, []]]]]]]]]]]]], 10];
    }

    public static function boolProvider(): iterable
    {
        yield 'bool(true)' => [true, 1231];
        yield 'bool(false)' => [false, 1237];
    }

    public static function floatProvider(): iterable
    {
        yield 'double-positive' => [1.1, 1066192077];
        yield 'double-negative' => [-1.1, -1081291571];
        yield 'float-positive' => [PHP_INT_MAX + 2, 1593835520];
        yield 'float-negative' => [PHP_INT_MIN - 2, -553648128];
    }

    public static function intProvider(): iterable
    {
        yield 'int-positive' => [1, 1];
        yield 'int-negative' => [-12, -12];
        yield 'int-max' => [PHP_INT_MAX, PHP_INT_MAX];
        yield 'int-min' => [PHP_INT_MIN, PHP_INT_MIN];
    }

    public static function objectProvider(): iterable
    {
        $obj = new stdClass();

        yield 'object' => [$obj, spl_object_id($obj)];
    }

    public static function resourceProvider(): iterable
    {
        $resource = self::createResource();
        yield 'resource' => [$resource, (int) $resource];

        fclose($resource);
        yield 'resource(closed)' => [$resource, (int) $resource];
    }

    public static function stringProvider(): iterable
    {
        yield 'string(foo)' => ['foo', 101574];
        yield 'string(foo bar)' => ['foo bar', -682507847];
        yield 'string(@)' => ['@', 64];
        yield 'string(À)' => ['À', 192];
        yield 'string(€)' => ['€', 8364];
    }

    /**
     * @return resource
     */
    private static function createResource()
    {
        $resource = fopen('php://memory', 'rb');
        if (is_resource($resource)) {
            return $resource;
        }

        throw new RuntimeException('Cannot create resource "php://memory"');
    }

    #[DataProvider('anyProvider')]
    public function testForAny(mixed $value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forAny($value));
    }

    /**
     * @param mixed[] $value
     */
    #[DataProvider('arrayProvider')]
    public function testForArray(array $value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forArray($value));
    }

    #[DataProvider('boolProvider')]
    public function testForBool(bool $value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forBool($value));
    }

    #[DataProvider('floatProvider')]
    public function testForFloat(float $value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forFloat($value));
    }

    #[DataProvider('intProvider')]
    public function testForInt(int $value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forInt($value));
    }

    #[DataProvider('objectProvider')]
    public function testForObject(object $value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forObject($value));
    }

    /**
     * @param resource $value
     */
    #[DataProvider('resourceProvider')]
    public function testForResource($value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forResource($value));
    }

    #[DataProvider('stringProvider')]
    public function testForString(string $value, int $hashCode): void
    {
        self::assertEquals($hashCode, HashCode::forString($value));
    }
}
