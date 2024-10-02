<?php

declare(strict_types=1);

namespace ParTest\Core\Unit;

use DomainException;
use Par\Core\Exception\NoSuchElementException;
use Par\Core\Optional;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
final class OptionalTest extends TestCase
{
    public static function allValuesProvider(): iterable
    {
        yield from self::nonNullableValuesProvider();
        yield [null];
    }

    public static function equalsProvider(): iterable
    {
        yield 'both-empty' => [Optional::empty(), Optional::empty(), true];
        yield 'same-value' => [Optional::fromAny('foo'), Optional::fromAny('foo'), true];
        yield 'different-value' => [Optional::fromAny('foo'), Optional::fromAny('bar'), false];
        yield 'value-vs-empty' => [Optional::fromAny('foo'), Optional::empty(), false];
        yield 'value-vs-null' => [Optional::fromAny('foo'), null, false];
    }

    public static function nonNullableValuesProvider(): iterable
    {
        yield ['foo'];
        yield [''];
        yield [true];
        yield [false];
        yield [0];
        yield [1];
        yield [new stdClass()];
        yield [[]];
    }

    public function testFilterReturnsEmptyOptionalWhenPredicateNotMatches(): void
    {
        $optional = Optional::fromAny('bar');

        self::assertEquals(Optional::empty(), $optional->filter(static fn(string $value): bool => 'bar' !== $value));
    }

    public function testFilterReturnsOptionalWhenPredicateMatches(): void
    {
        $optional = Optional::fromAny('foo');

        self::assertEquals($optional, $optional->filter(static fn(string $value): bool => 'foo' === $value));
    }

    public function testIfPresentDoesNotExecuteActionIfEmpty(): void
    {
        $optional = Optional::empty();

        $invocations = [];
        $optional->ifPresent(
            static function(?string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
        );

        self::assertEquals([], $invocations);
    }

    public function testIfPresentExecutesActionIfNotEmpty(): void
    {
        $optional = Optional::fromAny('foo');

        $invocations = [];
        $optional->ifPresent(
            static function(string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
        );

        self::assertEquals(['foo'], $invocations);
    }

    public function testIfPresentOrElseEmptyExecutesActionIfEmpty(): void
    {
        $optional = Optional::empty();

        $invocations = [];
        $optional->ifPresentOrElse(
            static function(?string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
            static function() use (&$invocations): void {
                $invocations[] = 'empty';
            },
        );

        self::assertEquals(['empty'], $invocations);
    }

    public function testIfPresentOrElseExecutesActionIfNotEmpty(): void
    {
        $optional = Optional::fromAny('foo');

        $invocations = [];
        $optional->ifPresentOrElse(
            static function(string $value) use (&$invocations): void {
                $invocations[] = $value;
            },
            static function() use (&$invocations): void {
                $invocations[] = '<empty>';
            },
        );

        self::assertEquals(['foo'], $invocations);
    }

    #[DataProvider('equalsProvider')]
    public function testItCanDetermineEquality(Optional $subject, mixed $other, bool $expected): void
    {
        self::assertEquals($expected, $subject->equals($other));
    }

    public function testItHasNoValueWhenConstructedEmpty(): void
    {
        $optional = Optional::empty();

        self::assertFalse($optional->isPresent());
        self::assertTrue($optional->isEmpty());

        $this->expectException(NoSuchElementException::class);
        $optional->get();
    }

    #[DataProvider('allValuesProvider')]
    public function testItHasValueWhenConstructedFromAny(mixed $a): void
    {
        $optional = Optional::fromAny($a);

        self::assertTrue($optional->isPresent());
        self::assertFalse($optional->isEmpty());
        self::assertEquals($a, $optional->get());
    }

    #[DataProvider('nonNullableValuesProvider')]
    public function testItHasValueWhenConstructedFromNullableWithNonNull(mixed $a): void
    {
        $optional = Optional::fromNullable($a);

        self::assertTrue($optional->isPresent());
        self::assertFalse($optional->isEmpty());
        self::assertEquals($a, $optional->get());
    }

    public function testItIsEmptyWhenConstructedFromNullableWithNull(): void
    {
        $optional = Optional::fromNullable(null);

        self::assertFalse($optional->isPresent());
        self::assertTrue($optional->isEmpty());

        $this->expectException(NoSuchElementException::class);
        $optional->get();
    }

    public function testMapReturnsEmptyOptionalWhenEmpty(): void
    {
        $optional = Optional::empty();

        self::assertEquals(Optional::empty(), $optional->map(static fn(?string $value): string => $value . '-mapped'));
    }

    public function testMapReturnsOptionalWithResultFromMapperWhenNotEmpty(): void
    {
        $optional = Optional::fromAny('foo');

        self::assertEquals(
            Optional::fromAny('foo-mapped'),
            $optional->map(static fn(string $value): string => $value . '-mapped')
        );
    }

    public function testOrElseGetReturnsResponseFromSupplierWhenEmpty(): void
    {
        $optional = Optional::empty();

        $otherValue = 'foo';

        self::assertEquals($otherValue, $optional->orElseGet(static fn(): string => $otherValue));
    }

    public function testOrElseGetReturnsValueWhenNotEmpty(): void
    {
        $value = 'foo';
        $optional = Optional::fromAny($value);

        $otherValue = 'bar';

        self::assertEquals($value, $optional->orElseGet(static fn(): string => $otherValue));
    }

    public function testOrElseReturnsOtherValueWhenEmpty(): void
    {
        $optional = Optional::empty();

        $otherValue = 'bar';

        self::assertEquals($otherValue, $optional->orElse($otherValue));
    }

    public function testOrElseReturnsValueWhenNotEmpty(): void
    {
        $value = 'foo';
        $optional = Optional::fromAny($value);

        $otherValue = 'bar';

        self::assertEquals($value, $optional->orElse($otherValue));
    }

    public function testOrElseThrowThrowsExceptionFromSupplierWhenNoValueIsPresent(): void
    {
        $optional = Optional::empty();

        $exception = new class extends DomainException {
        };
        $this->expectExceptionObject($exception);
        $optional->orElseThrow(static fn() => $exception);
    }
}
