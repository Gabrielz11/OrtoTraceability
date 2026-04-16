<?php

namespace App\Http\Controllers;

use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Material\Domain\Models\Material;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'total_in_stock' => Material::where('status', 'em_estoque')->count(),
            'total_reserved' => Material::where('status', 'reservado')->count(),
            'near_expiry' => Material::where('status', '!=', 'implantado_usado')
            ->get()
            ->filter(fn($m) => $m->isNearExpiry())
            ->count(),
            'expired' => Material::where('status', '!=', 'implantado_usado')
            ->get()
            ->filter(fn($m) => $m->isExpired())
            ->count(),
        ];

        $recent_audits = AuditEvent::with(['user', 'entity' => function ($query) {
            $query->withTrashed();
        }])->latest()->take(10)->get();

        $near_expiry_items = Material::where('status', '!=', 'implantado_usado')
            ->get()
            ->filter(fn($m) => $m->isNearExpiry())
            ->take(5);

        return view('dashboard', compact('kpis', 'recent_audits', 'near_expiry_items'));
    }
}
