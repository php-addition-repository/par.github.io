<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

/**
 * This comparator reverses the results of the comparator it receives.
 *
 * @template TValue
 *
 * @implements Comparator<TValue>
 */
final class ReverseComparator implements Comparator
{
    /**
     * @use InvokableComparatorTrait<TValue>
     */
    use InvokableComparatorTrait;

    /**
     * @use ThenableComparatorTrait<TValue>
     */
    use ThenableComparatorTrait;

    /**
     * @use UsingComparatorTrait<TValue>
     */
    use UsingComparatorTrait;

    /**
     * @param Comparator<TValue> $reversedComparator
     */
    public function __construct(private readonly Comparator $reversedComparator)
    {
    }

    public function compare(mixed $v1, mixed $v2): Order
    {
        return $this->reversedComparator->compare($v1, $v2)->invert();
    }

    public function reversed(): Comparator
    {
        return $this->reversedComparator;
    }
}
