<?php

namespace App\Http\Controllers;

use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Material\Application\Services\MaterialCrudService;
use App\Modules\Material\Domain\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct(
        private readonly MaterialCrudService $service,
    ) {}

    public function index(Request $request)
    {
        $query = Material::query();

        if ($request->nome) {
            $query->where('nome', 'like', "%{$request->nome}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->lote) {
            $query->where('lote', 'like', "%{$request->lote}%");
        }

        $materials = $query->latest()->paginate(15);
        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'lote' => 'required|string',
            'numero_serie' => 'nullable|string|unique:material_items,numero_serie',
            'validade' => 'required|date',
            'fabricante' => 'required|string',
            'status' => 'required|in:em_estoque,reservado,implantado_usado,descartado,devolvido_ao_fornecedor',
            'observacoes' => 'nullable|string',
        ]);

        $material = $this->service->store($validated);

        return redirect()->route('materials.show', $material)
            ->with('success', 'Material cadastrado com sucesso.');
    }

    public function show(Material $material)
    {
        $audits = AuditEvent::where('entity_type', 'material')
            ->where('entity_id', $material->id)
            ->latest()
            ->get();

        return view('materials.show', compact('material', 'audits'));
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'lote' => 'required|string',
            'numero_serie' => 'nullable|string|unique:material_items,numero_serie,' . $material->id,
            'validade' => 'required|date',
            'fabricante' => 'required|string',
            'status' => 'required|in:em_estoque,reservado,implantado_usado,descartado,devolvido_ao_fornecedor',
            'observacoes' => 'nullable|string',
        ]);

        $this->service->update($material, $validated);

        return redirect()->route('materials.show', $material)
            ->with('success', 'Material atualizado com sucesso.');
    }

    public function destroy(Material $material)
    {
        $this->service->delete($material);

        return redirect()->route('materials.index')
            ->with('success', 'Material removido com sucesso.');
    }
}
