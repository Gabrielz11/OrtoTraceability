<?php

namespace App\Http\Controllers;

use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Material\Application\Services\MaterialCrudService;
use App\Modules\Material\Domain\Events\MaterialAllocatedToSurgery;
use App\Modules\Material\Domain\Events\MaterialDiscarded;
use App\Modules\Material\Domain\Events\MaterialReturned;
use App\Modules\Material\Domain\Events\MaterialUsed;
use App\Modules\Material\Domain\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

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
        if ($request->numero_serie) {
            $query->where('numero_serie', 'like', "%{$request->numero_serie}%");
        }

        $materials = $query->latest()->paginate(10)->withQueryString();
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

    public function changeStatus(Request $request, Material $material)
    {
        $validated = $request->validate([
            'status' => 'required|in:em_estoque,reservado,implantado_usado,descartado,devolvido_ao_fornecedor',
            'surgery_id' => 'nullable|exists:surgeries,id',
            'observacoes' => 'nullable|string',
        ]);

        $newStatus = $validated['status'];
        $surgeryId = $validated['surgery_id'] ?? null;
        $actorId   = auth()->id();
        $actorRole = auth()->user()->role ?? 'admin';
        $now       = now()->toISOString();
        $meta      = ['ip' => $request->ip(), 'observacoes' => $validated['observacoes'] ?? null];

        if (in_array($newStatus, ['implantado_usado', 'reservado']) && ! $surgeryId) {
            return back()->with('error', 'Informe a cirurgia para este status.');
        }

        $material->update(['status' => $newStatus]);

        match ($newStatus) {
            'implantado_usado' => Event::dispatch(new MaterialUsed(
                materialId: $material->id,
                surgeryId:  (int) $surgeryId,
                actorId:    $actorId,
                actorRole:  $actorRole,
                occurredAt: $now,
                metadata:   $meta,
            )),
            'descartado' => Event::dispatch(new MaterialDiscarded(
                materialId: $material->id,
                actorId:    $actorId,
                actorRole:  $actorRole,
                occurredAt: $now,
                metadata:   $meta,
            )),
            'devolvido_ao_fornecedor' => Event::dispatch(new MaterialReturned(
                materialId: $material->id,
                actorId:    $actorId,
                actorRole:  $actorRole,
                occurredAt: $now,
                metadata:   $meta,
            )),
            'reservado' => Event::dispatch(new MaterialAllocatedToSurgery(
                materialId: $material->id,
                surgeryId:  (int) $surgeryId,
                actorId:    $actorId,
                actorRole:  $actorRole,
                occurredAt: $now,
                metadata:   $meta,
            )),
            default => null,
        };

        return back()->with('success', 'Status do material atualizado.');
    }
}
