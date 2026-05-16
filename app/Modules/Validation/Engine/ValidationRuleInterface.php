<?php

namespace App\Modules\Validation\Engine;

/**
 * Interface for all validation rules.
 *
 * Each rule inspects a material + surgery context and returns
 * a ValidationResult. Rules are composable — the engine runs
 * all of them and merges the results.
 */
interface ValidationRuleInterface
{
    /**
     * Validate a material in the context of a surgery.
     *
     * @param \App\Modules\Material\Domain\Models\Material $material
     * @param \App\Modules\Surgery\Domain\Models\Surgery|null $surgery
     * @return ValidationResult
     */
    public function validate($material, $surgery = null): ValidationResult;

    /**
     * Human-readable name of this rule (used in divergence reports).
     */
    public function ruleName(): string;
}
