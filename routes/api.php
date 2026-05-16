<?php

use App\Http\Resources\DivergenceResource;
use App\Http\Resources\MaterialLifecycleEventResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\SurgeryResource;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Material\Domain\Models\MaterialLifecycleEvent;
use App\Modules\Surgery\Domain\Models\Surgery;
use App\Modules\Validation\Domain\Models\Divergence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // ── Materials ───────────────────────────────────────────────
    Route::get('/materials', function (Request $request) {
        $query = Material::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->nome) {
            $query->where('nome', 'like', "%{$request->nome}%");
        }
        if ($request->lote) {
            $query->where('lote', 'like', "%{$request->lote}%");
        }
        if ($request->expiring_soon) {
            $query->whereRaw('validade <= ?', [now()->addDays(30)])
                  ->whereNotIn('status', ['implantado_usado', 'descartado', 'devolvido_ao_fornecedor']);
        }

        return MaterialResource::collection($query->latest()->paginate(25));
    });

    Route::get('/materials/{material}', function (Material $material) {
        $material->load('lifecycleEvents', 'surgeries');
        return new MaterialResource($material);
    });

    Route::get('/materials/{material}/timeline', function (Material $material) {
        $events = MaterialLifecycleEvent::where('material_id', $material->id)
            ->orderBy('occurred_at')
            ->get();

        return MaterialLifecycleEventResource::collection($events);
    });

    // ── Surgeries ───────────────────────────────────────────────
    Route::get('/surgeries', function (Request $request) {
        $query = Surgery::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return SurgeryResource::collection($query->latest()->paginate(25));
    });

    Route::get('/surgeries/{surgery}', function (Surgery $surgery) {
        $surgery->load('materials');
        return new SurgeryResource($surgery);
    });

    // ── Divergences ─────────────────────────────────────────────
    Route::get('/divergences', function (Request $request) {
        $query = Divergence::with(['material', 'surgery']);

        if ($request->severity) {
            $query->where('severity', $request->severity);
        }

        $query->where('status', $request->status ?? 'open');

        return DivergenceResource::collection($query->orderByDesc('occurred_at')->paginate(25));
    });

    // ── Dashboard Stats ─────────────────────────────────────────
    Route::get('/stats', function () {
        return response()->json([
            'totalInStock'        => Material::where('status', 'em_estoque')->count(),
            'totalReserved'       => Material::where('status', 'reservado')->count(),
            'nearExpiry'          => Material::whereNotIn('status', ['implantado_usado', 'descartado', 'devolvido_ao_fornecedor'])
                                        ->get()->filter(fn ($m) => $m->isNearExpiry())->count(),
            'expired'             => Material::whereNotIn('status', ['implantado_usado', 'descartado', 'devolvido_ao_fornecedor'])
                                        ->get()->filter(fn ($m) => $m->isExpired())->count(),
            'openDivergences'     => Divergence::where('status', 'open')->count(),
            'criticalDivergences' => Divergence::where('status', 'open')->where('severity', 'critical')->count(),
            'generatedAt'         => now()->toISOString(),
        ]);
    });
});
