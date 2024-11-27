---
linkTitle: Equable
title: "Par\\Core\\Equable"
draft: true
---

An object implementing this interface can determine if it equals any equable
value.

This is mainly useful because:

- Strict comparison (`$a === $b`) does not work on different instances of
  objects that represent the same value.
- Loose comparison (`$a == $b`) is possible, but you need to remember to use it
  on object comparison, but not on other value types which is confusing. By
  implementing this interface on the objects that require comparison you can use
  `$a->equals($b)` and you have all the control.

## Implements

### equals

Determines if this object should be considered equal to other value.

Signature: `equals(?\Par\Core\Equable $other): bool`

In most cases the method evaluates to `true` if the other value has the same
type and internal value(s) with an
implementation like the following.

```php
public function equals(?\Par\Core\Equable $other): bool
{
    if ($other instanceof self) {
        return $other->value === $this->value;
    }

    return false;
}
```
