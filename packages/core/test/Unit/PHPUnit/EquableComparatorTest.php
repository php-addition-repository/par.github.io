<?php

declare(strict_types=1);

namespace ParTest\Core\Unit\PHPUnit;

use Par\Core\Equable;
use Par\Core\PHPUnit\EquableComparator;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * @internal
 */
final class EquableComparatorTest extends TestCase
{
    public function testAccepts(): void
    {
        $comparator = new EquableComparator();

        $equableMock = $this->createMock(Equable::class);

        self::assertTrue($comparator->accepts($equableMock, $equableMock));
        self::assertFalse($comparator->accepts($equableMock, null));
        self::assertFalse($comparator->accepts($equableMock, 'bar'));
        self::assertFalse($comparator->accepts('bar', $equableMock));
    }

    public function testAssertEquals(): void
    {
        $comparator = new EquableComparator();

        $equableMock = $this->createMock(Equable::class);
        $equableMock->method('equals')->willReturnOnConsecutiveCalls(true, false);

        $comparator->assertEquals($equableMock, $equableMock);

        $this->expectException(ComparisonFailure::class);
        $this->expectExceptionMessage('Failed asserting that two objects are equal.');
        $comparator->assertEquals($equableMock, $equableMock);
    }
}
