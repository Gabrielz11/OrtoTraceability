<?php

namespace App\Modules\Kit\Domain\Models;

use App\Modules\Stock\Domain\Models\ProductTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitTemplateItem extends Model
{
    protected $table = 'kit_template_items';

    protected $fillable = [
        'kit_template_id', 'product_template_id',
        'quantidade_minima', 'quantidade_recomendada',
        'criticidade', 'observacoes',
    ];

    public function kitTemplate(): BelongsTo
    {
        return $this->belongsTo(KitTemplate::class);
    }

    public function productTemplate(): BelongsTo
    {
        return $this->belongsTo(ProductTemplate::class);
    }

    public function surgeryKitItems(): HasMany
    {
        return $this->hasMany(SurgeryKitItem::class);
    }
}
