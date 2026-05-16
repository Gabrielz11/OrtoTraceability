<?php

namespace App\Modules\Kit\Domain\Models;

use App\Modules\Stock\Domain\Models\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurgeryKitItem extends Model
{
    protected $table = 'surgery_kit_items';

    protected $fillable = [
        'surgery_kit_id', 'kit_template_item_id', 'stock_item_id',
        'categoria', 'resultado', 'motivo_descarte',
        'observacao_resultado', 'dentro_autorizacao',
    ];

    protected $casts = [
        'dentro_autorizacao' => 'boolean',
    ];

    public function surgeryKit(): BelongsTo
    {
        return $this->belongsTo(SurgeryKit::class);
    }

    public function kitTemplateItem(): BelongsTo
    {
        return $this->belongsTo(KitTemplateItem::class);
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function isPendente(): bool
    {
        return $this->resultado === 'pendente';
    }

    public function foiUtilizado(): bool
    {
        return in_array($this->resultado, ['implantado_usado', 'consumido']);
    }
}
