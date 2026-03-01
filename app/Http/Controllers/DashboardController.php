<?php

namespace App\Http\Controllers;

use App\Models\MaterialItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'total_in_stock' => MaterialItem::where('status', 'em_estoque')->count(),
            'total_reserved' => MaterialItem::where('status', 'reservado')->count(),
            'near_expiry' => MaterialItem::where('status', '!=', 'implantado_usado')
            ->get()
            ->filter(fn($m) => $m->isNearExpiry())
            ->count(),
            'expired' => MaterialItem::where('status', '!=', 'implantado_usado')
            ->get()
            ->filter(fn($m) => $m->isExpired())
            ->count(),
        ];

        $recent_audits = AuditLog::with('user')->latest()->take(10)->get();

        $near_expiry_items = MaterialItem::where('status', '!=', 'implantado_usado')
            ->get()
            ->filter(fn($m) => $m->isNearExpiry())
            ->take(5);

        return view('dashboard', compact('kpis', 'recent_audits', 'near_expiry_items'));
    }
}
