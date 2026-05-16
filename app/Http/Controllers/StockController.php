<?php

namespace App\Http\Controllers;

use App\Modules\Stock\Application\Services\StockService;
use App\Modules\Stock\Domain\Models\ProductTemplate;
use App\Modules\Stock\Domain\Models\StockItem;
use DomainException;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(
        private readonly StockService $service,
    ) {}

    public function index(Request $request)
    {
        $query = StockItem::with('productTemplate');

        if ($request->fabricante) {
            $query->whereHas('productTemplate', fn ($q) => $q->where('fabricante', $request->fabricante));
        }
        if ($request->tipo) {
            $query->whereHas('productTemplate', fn ($q) => $q->where('tipo', $request->tipo));
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->lote) {
            $query->where('lote', 'like', "%{$request->lote}%");
        }

        $stockItems  = $query->latest()->paginate(10)->withQueryString();
        $fabricantes = ProductTemplate::distinct()->orderBy('fabricante')->pluck('fabricante');

        return view('stock.index', compact('stockItems', 'fabricantes'));
    }

    public function create()
    {
        $products = ProductTemplate::where('ativo', true)->orderBy('fabricante')->orderBy('nome')->get();
        return view('stock.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_template_id'   => 'required|exists:product_templates,id',
            'lote'                  => 'nullable|string',
            'numero_serie'          => 'nullable|string|unique:stock_items,numero_serie',
            'validade'              => 'nullable|date',
            'tamanho'               => 'nullable|string',
            'referencia_fabricante' => 'nullable|string',
            'quantidade'            => 'integer|min:1',
        ]);

        $item = $this->service->store($validated);

        return redirect()->route('stock.show', $item)
            ->with('success', 'Item de estoque cadastrado.');
    }

    public function show(StockItem $stock)
    {
        $stock->load('productTemplate');
        return view('stock.show', compact('stock'));
    }

    public function edit(StockItem $stock)
    {
        $products = ProductTemplate::where('ativo', true)->orderBy('nome')->get();
        return view('stock.edit', compact('stock', 'products'));
    }

    public function update(Request $request, StockItem $stock)
    {
        $validated = $request->validate([
            'lote'                  => 'nullable|string',
            'numero_serie'          => 'nullable|string|unique:stock_items,numero_serie,' . $stock->id,
            'validade'              => 'nullable|date',
            'tamanho'               => 'nullable|string',
            'referencia_fabricante' => 'nullable|string',
        ]);

        $this->service->update($stock, $validated);

        return redirect()->route('stock.show', $stock)->with('success', 'Item atualizado.');
    }

    public function discard(Request $request, StockItem $stock)
    {
        $request->validate([
            'motivo'     => 'required|in:contaminacao,queda,quebra,uso_incorreto,vencimento,outro',
            'observacao' => 'nullable|string',
        ]);

        try {
            $this->service->discard($stock, $request->motivo, $request->observacao);
            return back()->with('success', 'Item descartado.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(StockItem $stock)
    {
        $this->service->delete($stock);
        return redirect()->route('stock.index')->with('success', 'Item removido.');
    }
}
