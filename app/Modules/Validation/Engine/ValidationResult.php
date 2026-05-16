<?php

namespace App\Modules\Validation\Engine;

/**
 * Immutable result of a validation pass.
 *
 * Contains a list of divergences found (empty = all rules passed).
 * Each divergence is an associative array with at least 'rule' and 'message' keys.
 */
class ValidationResult
{
    /**
     * @param array<int, array{rule: string, message: string, severity: string, context: array}> $divergences
     */
    public function __construct(
        public readonly array $divergences = [],
    ) {}

    public function passed(): bool
    {
        return empty($this->divergences);
    }

    public function failed(): bool
    {
        return !$this->passed();
    }

    public function count(): int
    {
        return count($this->divergences);
    }

    /**
     * Filter divergences by severity level.
     *
     * @return array<int, array>
     */
    public function bySeverity(string $severity): array
    {
        return array_values(
            array_filter($this->divergences, fn(array $d) => $d['severity'] === $severity)
        );
    }

    public function hasCritical(): bool
    {
        return !empty($this->bySeverity('critical'));
    }

    public function hasWarnings(): bool
    {
        return !empty($this->bySeverity('warning'));
    }

    /**
     * Merge another result into this one.
     */
    public function merge(ValidationResult $other): self
    {
        return new self(
            array_merge($this->divergences, $other->divergences)
        );
    }

    /**
     * Convert to array for event payload / JSON serialization.
     */
    public function toArray(): array
    {
        return [
            'passed'      => $this->passed(),
            'total'       => $this->count(),
            'critical'    => count($this->bySeverity('critical')),
            'warnings'    => count($this->bySeverity('warning')),
            'divergences' => $this->divergences,
        ];
    }
}
