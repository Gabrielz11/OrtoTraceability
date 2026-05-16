<?php

namespace App\Modules\Kit\Application\Services;

use App\Modules\Kit\Domain\Models\KitTemplate;
use App\Modules\Kit\Domain\Models\KitTemplateItem;
use Illuminate\Support\Facades\DB;

class KitTemplateService
{
    public function store(array $data, array $items = []): KitTemplate
    {
        return DB::transaction(function () use ($data, $items) {
            $template = KitTemplate::create($data);

            foreach ($items as $item) {
                KitTemplateItem::create(array_merge($item, [
                    'kit_template_id' => $template->id,
                ]));
            }

            return $template;
        });
    }

    public function update(KitTemplate $template, array $data): KitTemplate
    {
        $template->update($data);
        return $template;
    }

    public function addItem(KitTemplate $template, array $itemData): KitTemplateItem
    {
        return KitTemplateItem::create(array_merge($itemData, [
            'kit_template_id' => $template->id,
        ]));
    }

    public function removeItem(KitTemplateItem $item): void
    {
        $item->delete();
    }

    public function delete(KitTemplate $template): void
    {
        $template->update(['ativo' => false]);
    }
}
