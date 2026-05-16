<?php

namespace App\Http\Controllers;

use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Kit\Domain\Models\SurgeryKit;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Stock\Application\Services\StockAlertService;
use App\Modules\Stock\Domain\Models\StockItem;
use App\Modules\Surgery\Domain\Models\Surgery;
use App\Modules\Validation\Domain\Models\Divergence;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = Cache::remember('dashboard.kpis', 300, function () {
            return [
                'total_in_stock'       => Material::where('status', 'em_estoque')->count(),
                'total_reserved'       => Material::where('status', 'reservado')->count(),
                'near_expiry'          => app(StockAlertService::class)->itensProximosVencimento()->count()
                                         + Material::whereNotIn('status', ['implantado_usado', 'descartado', 'devolvido_ao_fornecedor'])
                                             ->get()->filter(fn ($m) => $m->isNearExpiry())->count(),
                'expired'              => Material::whereNotIn('status', ['implantado_usado', 'descartado', 'devolvido_ao_fornecedor'])
                                             ->get()->filter(fn ($m) => $m->isExpired())->count(),
                'open_divergences'     => Divergence::where('status', 'open')->count(),
                'critical_divergences' => Divergence::where('status', 'open')->where('severity', 'critical')->count(),
                'kits_em_separacao'    => SurgeryKit::where('status', 'em_separacao')->count(),
                'sem_autorizacao'      => Surgery::where('status', 'agendada')
                                             ->whereDoesntHave('authorization')->count(),
            ];
        });

        $recent_audits = AuditEvent::with(['user', 'entity' => function ($query) {
            $query->withTrashed();
        }])->latest()->take(10)->get();

        $near_expiry_items = Material::whereNotIn('status', ['implantado_usado', 'descartado', 'devolvido_ao_fornecedor'])
            ->get()
            ->filter(fn ($m) => $m->isNearExpiry())
            ->take(5);

        return view('dashboard', compact('kpis', 'recent_audits', 'near_expiry_items'));
    }
}
