<?php

declare(strict_types=1);

namespace Par\Core\Comparison;

/**
 * @template TValue
 *
 * @mixin Comparator<TValue>
 */
trait UsingComparatorTrait
{
    public function using(callable $extractor): Comparator
    {
        return new ExtractorComparator($extractor, $this);
    }
}
