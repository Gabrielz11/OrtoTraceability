<?php

namespace App\Http\Controllers;

use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Surgery\Application\Services\SurgeryMaterialService;
use App\Modules\Surgery\Application\Services\SurgeryService;
use App\Modules\Surgery\Domain\Models\Surgery;
use DomainException;
use Illuminate\Http\Request;

class SurgeryController extends Controller
{
    public function __construct(
        private readonly SurgeryService $surgeryService,
        private readonly SurgeryMaterialService $materialService,
    ) {}

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

        $surgery = $this->surgeryService->store($validated);

        return redirect()->route('surgeries.show', $surgery)
            ->with('success', 'Cirurgia cadastrada com sucesso.');
    }

    public function show(Surgery $surgery)
    {
        $audits = AuditEvent::where('entity_type', 'surgery')
            ->where('entity_id', $surgery->id)
            ->latest()
            ->get();

        $available_materials = Material::where('status', 'em_estoque')->get();

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

        $this->surgeryService->update($surgery, $validated);

        return redirect()->route('surgeries.show', $surgery)
            ->with('success', 'Cirurgia atualizada com sucesso.');
    }

    public function linkMaterial(Request $request, Surgery $surgery)
    {
        $request->validate(['material_id' => 'required|exists:material_items,id']);
        $material = Material::findOrFail($request->material_id);

        try {
            $this->materialService->linkMaterial($surgery, $material);
            return back()->with('success', 'Material vinculado com sucesso.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unlinkMaterial(Surgery $surgery, Material $material)
    {
        try {
            $this->materialService->unlinkMaterial($surgery, $material);
            return back()->with('success', 'Material desvinculado.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markAsUsed(Surgery $surgery, Material $material)
    {
        try {
            $this->materialService->markAsUsed($surgery, $material);
            return back()->with('success', 'Material marcado como usado.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Surgery $surgery)
    {
        $this->surgeryService->delete($surgery);

        return redirect()->route('surgeries.index')
            ->with('success', 'Cirurgia removida com sucesso.');
    }
}
