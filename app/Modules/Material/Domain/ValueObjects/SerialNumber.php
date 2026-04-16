<?php

namespace App\Modules\Material\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable value object representing a material serial number.
 * Nullable by design — serial numbers are optional per the PRD.
 */
final class SerialNumber
{
    private readonly ?string $value;

    public function __construct(?string $value)
    {
        if ($value !== null) {
            $value = trim($value);
            if ($value === '') {
                $value = null;
            }
        }

        $this->value = $value;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === null;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
