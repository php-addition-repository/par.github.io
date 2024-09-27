<?php

declare(strict_types=1);

namespace Par\Core;

/**
 * An object implementing this interface has the ability to generate a hash to represent its value and allows it to be
 * used as key in a hash table.
 *
 * It's an alternative to `spl_object_hash()` and `spl_object_id()`, which determines an object's hash/id based on its
 * handle: this means that two objects that are considered equal by an implicit definition would not be treated as
 * equal because they are not the same instance.
 */
interface Hashable
{
    /**
     * Returns a hash code value for the object.
     *
     * This method is supported for the benefit of hash tables such as those provided by a HashMap.
     * The general contract of hashCode is:
     *
     * - Whenever it is invoked on the same object more than once during an execution of an application, the hashCode
     * method must consistently return the same integer, provided no information used in equals comparisons on the
     * object is modified. This integer need not remain consistent from one execution of an application to another
     * execution of the same application.
     * - If two objects are equal according to the equals method, then calling the hashCode method on each of the two
     * objects must produce the same integer result.
     * - It is not required that if two objects are unequal according to the equals method, then calling the hashCode
     * method on each of the two objects must produce distinct integer results. However, the programmer should be aware
     * that producing distinct integer results for unequal objects may improve the performance of hash tables.
     *
     * An implementation would be:
     * ```
     * public function hashCode(): int
     * {
     *     return Values::hash($this->x, $this->y, $this->z);
     * }
     * ```
     */
    public function hashCode(): int;
}
