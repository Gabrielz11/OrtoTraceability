<?php

namespace App\Http\Controllers;

use App\Modules\Kit\Application\Services\KitTemplateService;
use App\Modules\Kit\Domain\Models\KitTemplate;
use App\Modules\Kit\Domain\Models\KitTemplateItem;
use App\Modules\Stock\Application\Services\StockAlertService;
use App\Modules\Stock\Domain\Models\ProductTemplate;
use Illuminate\Http\Request;

class KitTemplateController extends Controller
{
    public function __construct(
        private readonly KitTemplateService $service,
        private readonly StockAlertService $alerts,
    ) {}

    public function index()
    {
        $templates        = KitTemplate::with('items.productTemplate')->where('ativo', true)->get();
        $insufficientKits = $this->alerts->kitsComEstoqueInsuficiente();

        return view('kits.templates.index', compact('templates', 'insufficientKits'));
    }

    public function create()
    {
        $products = ProductTemplate::where('ativo', true)->orderBy('fabricante')->orderBy('nome')->get();
        return view('kits.templates.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'         => 'required|string|max:255',
            'fabricante'   => 'required|string',
            'procedimento' => 'required|string',
            'tipo_kit'     => 'required|in:implante,instrumental,consumivel',
            'descricao'    => 'nullable|string',
        ]);

        $template = $this->service->store($validated);

        return redirect()->route('kit-templates.show', $template)
            ->with('success', 'Template de kit criado com sucesso.');
    }

    public function show(KitTemplate $kitTemplate)
    {
        $kitTemplate->load('items.productTemplate');
        $products = ProductTemplate::where('ativo', true)->orderBy('nome')->get();

        return view('kits.templates.show', compact('kitTemplate', 'products'));
    }

    public function addItem(Request $request, KitTemplate $kitTemplate)
    {
        $validated = $request->validate([
            'product_template_id'    => 'required|exists:product_templates,id',
            'quantidade_minima'      => 'required|integer|min:0',
            'quantidade_recomendada' => 'required|integer|min:1',
            'criticidade'            => 'required|in:essencial,sobressalente,condicional',
            'observacoes'            => 'nullable|string',
        ]);

        $this->service->addItem($kitTemplate, $validated);

        return back()->with('success', 'Item adicionado ao template.');
    }

    public function removeItem(KitTemplate $kitTemplate, KitTemplateItem $item)
    {
        $this->service->removeItem($item);
        return back()->with('success', 'Item removido do template.');
    }

    public function destroy(KitTemplate $kitTemplate)
    {
        $this->service->delete($kitTemplate);
        return redirect()->route('kit-templates.index')->with('success', 'Template desativado.');
    }
}
