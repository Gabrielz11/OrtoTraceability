<?php

namespace App\Http\Controllers;

use App\Modules\Validation\Domain\Models\Divergence;
use Illuminate\Http\Request;

class DivergenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Divergence::with(['material', 'surgery', 'acknowledgedBy'])
            ->orderByDesc('occurred_at');

        if ($request->severity) {
            $query->where('severity', $request->severity);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $divergences = $query->paginate(25)->withQueryString();

        return view('divergences.index', compact('divergences'));
    }

    public function acknowledge(Request $request, Divergence $divergence)
    {
        if (! $divergence->isOpen()) {
            return back()->with('error', 'Divergência já foi tratada.');
        }

        $divergence->update([
            'status'          => 'acknowledged',
            'acknowledged_by' => auth()->id(),
            'acknowledged_at' => now(),
        ]);

        return back()->with('success', 'Divergência reconhecida.');
    }
}
