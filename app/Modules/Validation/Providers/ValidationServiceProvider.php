<?php

namespace App\Modules\Validation\Providers;

use App\Modules\Validation\Engine\ValidationEngine;
use App\Modules\Validation\Rules\MaterialAllocatedBeforeUseRule;
use App\Modules\Validation\Rules\MaterialNearExpiryWarningRule;
use App\Modules\Validation\Rules\MaterialNotExpiredRule;
use App\Modules\Validation\Rules\SurgeryNotCancelledRule;
use Illuminate\Support\ServiceProvider;

/**
 * Registers the ValidationEngine in the service container
 * with the default set of rules for material usage validation.
 *
 * The engine is registered as a singleton so all listeners
 * share the same instance and rule set.
 */
class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ValidationEngine::class, function () {
            return new ValidationEngine([
                new MaterialNotExpiredRule(),
                new MaterialAllocatedBeforeUseRule(),
                new MaterialNearExpiryWarningRule(),
                new SurgeryNotCancelledRule(),
            ]);
        });
    }
}
