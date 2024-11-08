<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

use Closure;
use TypeError;

/**
 * @template TValue
 *
 * @implements Comparator<TValue>
 */
final class CallableComparator implements Comparator
{
    /**
     * @use InvokableComparatorTrait<TValue>
     */
    use InvokableComparatorTrait;

    /**
     * @use ReversibleComparatorTrait<TValue>
     */
    use ReversibleComparatorTrait;

    /**
     * @use ThenableComparatorTrait<TValue>
     */
    use ThenableComparatorTrait;

    /**
     * @use UsingComparatorTrait<TValue>
     */
    use UsingComparatorTrait;

    /**
     * @var Closure(TValue, TValue):(Order|int<-1,1>)
     */
    private readonly Closure $comparator;

    /**
     * @param callable(TValue, TValue):(Order|int<-1,1>) $comparator
     */
    public function __construct(callable $comparator)
    {
        $this->comparator = $comparator(...);
    }

    public function compare(mixed $v1, mixed $v2): Order
    {
        $comparator = $this->comparator;

        return Order::castOrThrow(
            $comparator($v1, $v2),
            static fn(mixed $value): TypeError => new TypeError(
                sprintf(
                    'Return value of callable comparator provided to %s must be %s|int<1,-1>, got %s',
                    self::class,
                    Order::class,
                    get_debug_type($value),
                ),
            ),
        );
    }
}
