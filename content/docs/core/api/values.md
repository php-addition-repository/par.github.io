---
linkTitle: Values
title: "Par\\Core\\Values"
draft: true
---

## Static methods

### equals

Determines if two values should be considered equal.

- If both values implement [`\Par\Core\Equable`](../equable) then
  `$value->equals($otherValue)` is used.
- When both values are instances of `\DateTime` or `\DateTimeImmutable` then
  `$value == $otherValue` is used.
- Otherwise, a strict comparison (`$value === $otherValue`) is used.

Signature: `\Par\Core\Values::equals(mixed $value, mixed $otherValue): bool`

```php
if (\Par\Core\Values::equals($a, $b)) {
    // $a and $b are equal
}
```

### equalsAnyIn

Determines if a value should be considered equal to __any__ of the items in the
list of other values.

Signature:
`\Par\Core\Values::equalsAnyIn(mixed $value, iterable $otherValues): bool`

```php
$otherValues = [$b, $c];
if (Values::equalsAnyIn($a, $otherValues)) {
    // $a is equal to any value in $otherValues
}
```

### equalsAnyOf

Determines if a value should be considered equal to __any__ of the other values.

Signature:
`\Par\Core\Values::equalsAnyOf(mixed $value, mixed ...$otherValues): bool`

```php
if (Values::equalsAnyIn($a, $b, $c)) {
    // $a is equal to $b OR $c
}
```

### equalsNoneIn

Determines if a value should be considered equal to __none__ of the items in the
list of other values.

Signature:
`\Par\Core\Values::equalsNoneIn(mixed $value, iterable $otherValues): bool`

```php
$otherValues = [$b, $c];
if (Values::equalsNoneIn($a, $otherValues)) {
     // $a is not equal to any value in $otherValues
}
```

### equalsNoneOf

Determines if a value should be considered equal to __none__ of the other
values.

Signature: `\Par\Core\Values::equals(mixed $value, mixed ...$otherValues): bool`

```php
if (Values::equalsNoneIn($a, $b, $c)) {
    // $a is not equal to $b AND $c
}
```

### hash

Generates a hash code for a sequence of input values.

The hash code is generated as if all the input values were placed into an array,
and that array were hashed by calling `\Par\Core\Values->hashCode([...])`. This
method is
useful for implementing [`\Par\Core\Hashable::hashCode()`](../hashable) on
objects containing
multiple fields. For example, if an object that has three properties, `$x`,
`$y`, and `$z`, one could write:

```php
public function hashCode(): int
{
    return \Par\Core\Values::hash($this->x, $this->y, $this->z);
}
```

Signature: `\Par\Core\Values::equals(mixed $value, mixed $otherValue): bool`

### hashCode

Returns the hash code of a non-null argument and 0 for a null argument.

- If the `$value` implements [`\Par\Core\Hashable`](../hashable) then
  `$value->hashCode()` is
  returned.
- Otherwise, an internal solution is used to calculate a hash code for provided
  value.

Signature: `\Par\Core\Values::hashCode(mixed $value): int`
