<?php

namespace App\Modules\Material\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable value object representing a material batch/lot number.
 * Validates that the batch number is non-empty on construction.
 */
final class BatchNumber
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Batch number (lote) cannot be empty.');
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
