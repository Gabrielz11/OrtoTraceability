<?php

namespace App\Http\Controllers;

use App\Modules\Kit\Application\Services\SurgeryKitService;
use App\Modules\Kit\Domain\Models\KitTemplate;
use App\Modules\Kit\Domain\Models\SurgeryKit;
use App\Modules\Kit\Domain\Models\SurgeryKitItem;
use App\Modules\Stock\Domain\Models\StockItem;
use App\Modules\Surgery\Domain\Models\Surgery;
use DomainException;
use Illuminate\Http\Request;

class SurgeryKitController extends Controller
{
    public function __construct(
        private readonly SurgeryKitService $service,
    ) {}

    public function create(Surgery $surgery)
    {
        $templates = KitTemplate::where('ativo', true)->orderBy('fabricante')->get();
        return view('kits.surgery.create', compact('surgery', 'templates'));
    }

    public function store(Request $request, Surgery $surgery)
    {
        $request->validate([
            'kit_template_id' => 'required|exists:kit_templates,id',
        ]);

        $template = KitTemplate::with('items')->findOrFail($request->kit_template_id);

        try {
            $kit = $this->service->iniciarMontagem($surgery, $template);
            return redirect()->route('surgery-kits.show', $kit)
                ->with('success', 'Kit iniciado. Vincule os itens do estoque.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(SurgeryKit $surgeryKit)
    {
        $surgeryKit->load([
            'surgery.authorization',
            'kitTemplate',
            'items.kitTemplateItem.productTemplate',
            'items.stockItem.productTemplate',
            'conferidoPor',
        ]);

        $availableStock = StockItem::with('productTemplate')
            ->where('status', 'em_estoque')
            ->get()
            ->filter(fn ($s) => !$s->isExpired());

        return view('kits.surgery.show', compact('surgeryKit', 'availableStock'));
    }

    public function vincularItem(Request $request, SurgeryKit $surgeryKit, SurgeryKitItem $item)
    {
        $request->validate(['stock_item_id' => 'required|exists:stock_items,id']);

        $stockItem = StockItem::findOrFail($request->stock_item_id);

        try {
            $this->service->vincularItem($item, $stockItem);
            return back()->with('success', 'Item vinculado ao kit.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function desvincularItem(SurgeryKit $surgeryKit, SurgeryKitItem $item)
    {
        $this->service->desvincularItem($item);
        return back()->with('success', 'Item desvinculado.');
    }

    public function conferir(SurgeryKit $surgeryKit)
    {
        try {
            $this->service->conferir($surgeryKit);
            return back()->with('success', 'Kit conferido com sucesso.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function despachar(SurgeryKit $surgeryKit)
    {
        try {
            $this->service->despachar($surgeryKit);
            return back()->with('success', 'Kit despachado.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirmarRecebimento(SurgeryKit $surgeryKit)
    {
        try {
            $this->service->confirmarRecebimento($surgeryKit);
            return back()->with('success', 'Recebimento confirmado no hospital.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resultado(SurgeryKit $surgeryKit)
    {
        $surgeryKit->load(['items.kitTemplateItem.productTemplate', 'items.stockItem']);
        return view('kits.surgery.resultado', compact('surgeryKit'));
    }

    public function registrarResultados(Request $request, SurgeryKit $surgeryKit)
    {
        $request->validate([
            'itens'                          => 'required|array',
            'itens.*.resultado'              => 'required|in:implantado_usado,consumido,devolvido_intacto,descartado',
            'itens.*.motivo_descarte'        => 'nullable|string',
            'itens.*.observacao_resultado'   => 'nullable|string',
        ]);

        $errors = [];

        foreach ($request->itens as $itemId => $data) {
            $kitItem = SurgeryKitItem::findOrFail($itemId);
            try {
                $this->service->registrarResultado(
                    $kitItem,
                    $data['resultado'],
                    $data['motivo_descarte'] ?? null,
                    $data['observacao_resultado'] ?? null,
                );
            } catch (DomainException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return back()->with('error', implode(' | ', $errors));
        }

        return redirect()->route('surgeries.show', $surgeryKit->surgery_id)
            ->with('success', 'Resultados registrados com sucesso.');
    }

    public function devolver(SurgeryKit $surgeryKit)
    {
        try {
            $this->service->devolver($surgeryKit);
            return back()->with('success', 'Kit devolvido ao estoque.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
