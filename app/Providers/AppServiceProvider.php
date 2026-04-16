<?php

namespace App\Providers;

use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'material' => Material::class,
            'surgery'  => Surgery::class,
        ]);
    }
}
