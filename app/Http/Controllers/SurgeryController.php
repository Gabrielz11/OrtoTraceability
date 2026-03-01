<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use App\Models\MaterialItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurgeryController extends Controller
{
    public function index(Request $request)
    {
        $query = Surgery::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $surgeries = $query->latest()->paginate(15);
        return view('surgeries.index', compact('surgeries'));
    }

    public function create()
    {
        return view('surgeries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_hora' => 'required|date',
            'hospital' => 'required|string',
            'medico' => 'required|string',
            'paciente' => 'required|string',
            'status' => 'required|in:agendada,realizada,cancelada',
            'observacoes' => 'nullable|string',
        ]);

        $surgery = Surgery::create($validated);

        return redirect()->route('surgeries.show', $surgery)
            ->with('success', 'Cirurgia cadastrada com sucesso.');
    }

    public function show(Surgery $surgery)
    {
        $audits = AuditLog::where('entity_type', Surgery::class)
            ->where('entity_id', $surgery->id)
            ->latest()
            ->get();

        $available_materials = MaterialItem::where('status', 'em_estoque')->get();

        return view('surgeries.show', compact('surgery', 'audits', 'available_materials'));
    }

    public function edit(Surgery $surgery)
    {
        return view('surgeries.edit', compact('surgery'));
    }

    public function update(Request $request, Surgery $surgery)
    {
        $validated = $request->validate([
            'data_hora' => 'required|date',
            'hospital' => 'required|string',
            'medico' => 'required|string',
            'paciente' => 'required|string',
            'status' => 'required|in:agendada,realizada,cancelada',
            'observacoes' => 'nullable|string',
        ]);

        $surgery->update($validated);

        return redirect()->route('surgeries.show', $surgery)
            ->with('success', 'Cirurgia atualizada com sucesso.');
    }

    public function linkMaterial(Request $request, Surgery $surgery)
    {
        $request->validate(['material_id' => 'required|exists:material_items,id']);
        $material = MaterialItem::find($request->material_id);

        if ($material->status !== 'em_estoque') {
            return back()->with('error', 'Material não disponível para reserva.');
        }

        if ($material->isExpired()) {
            return back()->with('error', 'Não é possível vincular material vencido.');
        }

        DB::transaction(function () use ($surgery, $material) {
            $surgery->materials()->attach($material->id, ['acao' => 'reservado']);
            $material->update(['status' => 'reservado']);

            $surgery->logAudit('link', null, ['material_id' => $material->id], ['context' => 'linked material']);
        });

        return back()->with('success', 'Material vinculado com sucesso.');
    }

    public function unlinkMaterial(Surgery $surgery, MaterialItem $material)
    {
        $pivot = $surgery->materials()->where('material_item_id', $material->id)->first()->pivot;

        if ($pivot->acao === 'usado') {
            return back()->with('error', 'Não é possível desvincular um material que já foi usado.');
        }

        DB::transaction(function () use ($surgery, $material) {
            $surgery->materials()->detach($material->id);
            $material->update(['status' => 'em_estoque']);

            $surgery->logAudit('unlink', ['material_id' => $material->id], null, ['context' => 'unlinked material']);
        });

        return back()->with('success', 'Material desvinculado.');
    }

    public function markAsUsed(Surgery $surgery, MaterialItem $material)
    {
        if ($surgery->status === 'cancelada') {
            return back()->with('error', 'Cirurgia cancelada. Não é possível usar materiais.');
        }

        DB::transaction(function () use ($surgery, $material) {
            $surgery->materials()->updateExistingPivot($material->id, ['acao' => 'usado']);
            $material->update(['status' => 'implantado_usado']);

            $surgery->logAudit('status_change', ['material_id' => $material->id, 'old_acao' => 'reservado'], ['new_acao' => 'usado']);
        });

        return back()->with('success', 'Material marcado como usado.');
    }

    public function destroy(Surgery $surgery)
    {
        $surgery->delete();
        return redirect()->route('surgeries.index')
            ->with('success', 'Cirurgia removida com sucesso.');
    }
}
