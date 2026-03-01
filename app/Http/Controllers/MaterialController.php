<?php

namespace App\Http\Controllers;

use App\Models\MaterialItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialItem::query();

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
            'lote' => 'required|string',
            'numero_serie' => 'nullable|string|unique:material_items,numero_serie',
            'validade' => 'required|date',
            'fabricante' => 'required|string',
            'status' => 'required|in:em_estoque,reservado,implantado_usado,descartado,devolvido_ao_fornecedor',
            'observacoes' => 'nullable|string',
        ]);

        $material = MaterialItem::create($validated);

        return redirect()->route('materials.show', $material)
            ->with('success', 'Material cadastrado com sucesso.');
    }

    public function show(MaterialItem $material)
    {
        $audits = AuditLog::where('entity_type', MaterialItem::class)
            ->where('entity_id', $material->id)
            ->latest()
            ->get();

        return view('materials.show', compact('material', 'audits'));
    }

    public function edit(MaterialItem $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, MaterialItem $material)
    {
        $validated = $request->validate([
            'lote' => 'required|string',
            'numero_serie' => 'nullable|string|unique:material_items,numero_serie,' . $material->id,
            'validade' => 'required|date',
            'fabricante' => 'required|string',
            'status' => 'required|in:em_estoque,reservado,implantado_usado,descartado,devolvido_ao_fornecedor',
            'observacoes' => 'nullable|string',
        ]);

        $material->update($validated);

        return redirect()->route('materials.show', $material)
            ->with('success', 'Material atualizado com sucesso.');
    }

    public function destroy(MaterialItem $material)
    {
        $material->delete();
        return redirect()->route('materials.index')
            ->with('success', 'Material removido com sucesso.');
    }
}
