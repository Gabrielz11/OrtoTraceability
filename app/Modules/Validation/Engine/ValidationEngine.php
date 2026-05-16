<?php

namespace App\Modules\Validation\Engine;

use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Models\Surgery;

/**
 * Composable Validation Engine.
 *
 * Runs a configurable set of rules against a material/surgery pair
 * and produces a unified ValidationResult. Rules are injected via
 * constructor — the engine itself has zero knowledge of specific
 * business rules.
 *
 * Usage:
 *   $engine = new ValidationEngine([
 *       new MaterialNotExpiredRule(),
 *       new MaterialAllocatedBeforeUseRule(),
 *   ]);
 *   $result = $engine->validate($material, $surgery);
 */
class ValidationEngine
{
    /** @var ValidationRuleInterface[] */
    private array $rules;

    /**
     * @param ValidationRuleInterface[] $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * Add a rule to the engine at runtime.
     */
    public function addRule(ValidationRuleInterface $rule): self
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Run all registered rules and merge divergences into one result.
     */
    public function validate(Material $material, ?Surgery $surgery = null): ValidationResult
    {
        $result = new ValidationResult();

        foreach ($this->rules as $rule) {
            $ruleResult = $rule->validate($material, $surgery);
            $result = $result->merge($ruleResult);
        }

        return $result;
    }

    /**
     * Returns the list of registered rule names (for introspection/logging).
     *
     * @return string[]
     */
    public function registeredRules(): array
    {
        return array_map(fn(ValidationRuleInterface $r) => $r->ruleName(), $this->rules);
    }
}
