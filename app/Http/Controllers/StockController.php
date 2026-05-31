<?php

namespace App\Http\Controllers;

use App\Modules\Stock\Domain\Events\StockItemReceived;
use App\Modules\Stock\Domain\Models\ProductTemplate;
use App\Modules\Stock\Domain\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class StockController extends Controller
{
    // ─── TELA PRINCIPAL ────────────────────────────────────────────────────────

    public function index()
    {
        $products = ProductTemplate::where('ativo', true)
            ->withCount([
                'stockItems as total_em_estoque' => fn ($q) => $q->where('status', 'em_estoque'),
            ])
            ->with([
                'stockItemsInStock' => fn ($q) => $q->orderBy('validade'),
            ])
            ->when(request('search'), fn ($q) =>
                $q->where('nome', 'like', '%' . request('search') . '%')
                  ->orWhere('fabricante', 'like', '%' . request('search') . '%')
                  ->orWhere('codigo', 'like', '%' . request('search') . '%')
            )
            ->when(request('tipo'), fn ($q) => $q->where('tipo', request('tipo')))
            ->when(request('categoria'), fn ($q) => $q->where('categoria', request('categoria')))
            ->orderBy('fabricante')
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        $alerts = [
            'sem_estoque' => ProductTemplate::where('ativo', true)
                ->whereDoesntHave('stockItems', fn ($q) => $q->where('status', 'em_estoque'))
                ->count(),
            'near_expiry' => StockItem::where('status', 'em_estoque')
                ->whereNotNull('validade')
                ->whereBetween('validade', [now(), now()->addDays(30)])
                ->count(),
            'vencidos' => StockItem::where('status', 'em_estoque')
                ->whereNotNull('validade')
                ->where('validade', '<', now())
                ->count(),
        ];

        $tipos = [
            'implante_esteril' => 'Implante',
            'instrumental'     => 'Instrumental',
            'consumivel'       => 'Consumível',
        ];

        $categorias = [
            'protese_quadril'    => 'Prótese Quadril',
            'protese_joelho'     => 'Prótese Joelho',
            'protese_ombro'      => 'Prótese Ombro',
            'coluna'             => 'Coluna',
            'trauma'             => 'Trauma',
            'instrumental_geral' => 'Instrumental Geral',
            'consumivel_geral'   => 'Consumível Geral',
        ];

        return view('stock.index', compact('products', 'alerts', 'tipos', 'categorias'));
    }

    // ─── PRODUTO (CATÁLOGO) ────────────────────────────────────────────────────

    public function createProduct()
    {
        return view('stock.products.create');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'codigo'              => 'required|string|max:100|unique:product_templates,codigo',
            'nome'                => 'required|string|max:255',
            'fabricante'          => 'required|string|max:255',
            'tipo'                => 'required|in:implante_esteril,instrumental,consumivel',
            'categoria'           => 'required|in:protese_quadril,protese_joelho,protese_ombro,coluna,trauma,instrumental_geral,consumivel_geral',
            'codigo_anvisa'       => 'nullable|string|max:100',
            'unidade_medida'      => 'nullable|string|max:50',
            'requer_numero_serie' => 'boolean',
            'requer_lote'         => 'boolean',
            'observacoes'         => 'nullable|string',
        ]);

        $validated['requer_numero_serie'] = $request->boolean('requer_numero_serie');
        $validated['requer_lote']         = $request->boolean('requer_lote', true);

        ProductTemplate::create($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', "Produto \"{$validated['nome']}\" cadastrado no catálogo.");
    }

    public function editProduct(ProductTemplate $product)
    {
        return view('stock.products.edit', compact('product'));
    }

    public function updateProduct(Request $request, ProductTemplate $product)
    {
        $validated = $request->validate([
            'codigo'              => 'required|string|max:100|unique:product_templates,codigo,' . $product->id,
            'nome'                => 'required|string|max:255',
            'fabricante'          => 'required|string|max:255',
            'tipo'                => 'required|in:implante_esteril,instrumental,consumivel',
            'categoria'           => 'required|in:protese_quadril,protese_joelho,protese_ombro,coluna,trauma,instrumental_geral,consumivel_geral',
            'codigo_anvisa'       => 'nullable|string|max:100',
            'unidade_medida'      => 'nullable|string|max:50',
            'requer_numero_serie' => 'boolean',
            'requer_lote'         => 'boolean',
            'observacoes'         => 'nullable|string',
        ]);

        $validated['requer_numero_serie'] = $request->boolean('requer_numero_serie');
        $validated['requer_lote']         = $request->boolean('requer_lote', true);

        $product->update($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', "Produto \"{$product->nome}\" atualizado.");
    }

    // ─── ENTRADAS DE ESTOQUE ──────────────────────────────────────────────────

    public function createItem(ProductTemplate $product)
    {
        return view('stock.items.create', compact('product'));
    }

    public function storeItem(Request $request, ProductTemplate $product)
    {
        $validated = $request->validate([
            'lote'         => $product->requer_lote
                                ? 'required|string|max:100'
                                : 'nullable|string|max:100',
            'numero_serie' => $product->requer_numero_serie
                                ? 'required|string|max:100|unique:stock_items,numero_serie'
                                : 'nullable|string|max:100|unique:stock_items,numero_serie',
            'validade'     => 'nullable|date',
            'tamanho'      => 'nullable|string|max:50',
            'quantidade'   => 'required|integer|min:1|max:9999',
        ]);

        $item = $product->stockItems()->create(array_merge(
            $validated,
            ['status' => 'em_estoque']
        ));

        Event::dispatch(new StockItemReceived(
            stockItemId: $item->id,
            actorId:     auth()->id(),
            actorRole:   auth()->user()->role ?? 'admin',
            occurredAt:  now()->toISOString(),
            metadata:    ['ip' => request()->ip()],
        ));

        return redirect()
            ->route('stock.index')
            ->with('success', "Entrada registrada: {$product->nome}" .
                ($item->lote ? " · Lote {$item->lote}" : '') . '.');
    }

    // ─── SHOW (detalhe do produto com todos os itens) ──────────────────────────

    public function showProduct(ProductTemplate $product)
    {
        $product->load(['stockItems' => fn ($q) => $q->orderBy('validade')]);

        $grouped = $product->stockItems->groupBy('status');

        return view('stock.products.show', compact('product', 'grouped'));
    }
}
