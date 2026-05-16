<?php

namespace App\Providers;

use App\Modules\Kit\Domain\Models\SurgeryKit;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Stock\Domain\Models\StockItem;
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
            'kit'      => SurgeryKit::class,
            'stock'    => StockItem::class,
        ]);
    }
}
