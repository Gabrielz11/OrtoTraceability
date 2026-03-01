@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Dashboard</h1>
            <p class="text-text-secondary mt-1">Bem-vindo ao sistema de rastreabilidade OPME.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('materials.create') }}" class="px-5 py-2.5 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                Novo Material
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-border shadow-sm flex flex-col gap-2">
            <span class="text-xs font-bold text-text-secondary uppercase">Em Estoque</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-text-primary">{{ $kpis['total_in_stock'] }}</span>
                <span class="text-xs text-text-secondary">itens</span>
            </div>
            <div class="w-full bg-surface-light h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-primary h-full" style="width: 80%"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-border shadow-sm flex flex-col gap-2 border-l-4 border-l-blue-400">
            <span class="text-xs font-bold text-text-secondary uppercase">Reservados</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-text-primary">{{ $kpis['total_reserved'] }}</span>
                <span class="text-xs text-text-secondary">itens</span>
            </div>
            <p class="text-[10px] text-text-secondary italic">Aguardando procedimento</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-border shadow-sm flex flex-col gap-2 border-l-4 border-l-warning">
            <span class="text-xs font-bold text-text-secondary uppercase">Próximos ao Vencimento</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-warning">{{ $kpis['near_expiry'] }}</span>
                <span class="text-xs text-text-secondary">itens</span>
            </div>
            <p class="text-[10px] text-warning font-semibold">Alerta de validade (30 dias)</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-border shadow-sm flex flex-col gap-2 border-l-4 border-l-danger">
            <span class="text-xs font-bold text-text-secondary uppercase">Vencidos</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-danger">{{ $kpis['expired'] }}</span>
                <span class="text-xs text-text-secondary">itens</span>
            </div>
            <p class="text-[10px] text-danger font-semibold">Ação imediata necessária</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Near Expiry Items Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface">
                <h3 class="font-bold text-text-primary">Materiais em Risco</h3>
                <a href="{{ route('materials.index') }}" class="text-xs text-primary font-bold hover:underline underline-offset-4">Ver todos</a>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-light border-b border-border text-text-secondary">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-[10px] uppercase">Material</th>
                        <th class="px-6 py-3 font-semibold text-[10px] uppercase">Lote</th>
                        <th class="px-6 py-3 font-semibold text-[10px] uppercase">Validade</th>
                        <th class="px-6 py-3 font-semibold text-[10px] uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($near_expiry_items as $item)
                    <tr class="hover:bg-surface-light transition">
                        <td class="px-6 py-4 font-bold text-text-primary">
                            <a href="{{ route('materials.show', $item) }}" class="hover:underline">{{ $item->nome ?? 'Sem nome' }}</a>
                        </td>
                        <td class="px-6 py-4 text-text-secondary">{{ $item->lote }}</td>
                        <td class="px-6 py-4 {{ $item->isExpired() ? 'text-danger font-bold' : 'text-text-secondary' }}">
                            {{ $item->validade->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->isExpired())
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-danger border border-red-200 uppercase">Vencido</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-warning border border-amber-200 uppercase">Alerta</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-text-secondary italic">Nenhum item em risco crítico no momento.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white rounded-2xl border border-border shadow-sm flex flex-col">
            <div class="px-6 py-4 border-b border-border bg-surface">
                <h3 class="font-bold text-text-primary">Atividade Recente</h3>
            </div>
            <div class="p-6 flex flex-col gap-6 overflow-y-auto max-h-[400px]">
                @forelse($recent_audits as $audit)
                <div class="flex gap-4 relative">
                    @if(!$loop->last)
                    <div class="absolute left-3 top-6 bottom-0 w-px bg-border"></div>
                    @endif
                    <div class="w-6 h-6 rounded-full {{ $audit->action == 'create' ? 'bg-green-100 text-success' : ($audit->action == 'delete' ? 'bg-red-100 text-danger' : 'bg-blue-100 text-primary') }} flex items-center justify-center shrink-0 z-10 border border-white">
                        <span class="text-[10px] font-bold">{{ strtoupper(substr($audit->action, 0, 1)) }}</span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <p class="text-xs font-bold text-text-primary">
                            {{ ucfirst($audit->action) }} 
                            @if($audit->entity_type === 'App\Models\MaterialItem')
                                {{ $audit->entity?->nome ?? 'Material' }}
                            @elseif($audit->entity_type === 'App\Models\Surgery')
                                {{ $audit->entity?->paciente ?? 'Cirurgia' }}
                            @else
                                {{ class_basename($audit->entity_type) }}
                            @endif
                        </p>
                        <p class="text-[10px] text-text-secondary">{{ $audit->created_at->diffForHumans() }} • {{ $audit->user->name ?? 'Sistema' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-text-secondary italic text-xs py-4">Nenhuma atividade recente.</p>
                @endforelse
            </div>
            <a href="{{ route('audit.index') }}" class="mt-auto p-4 text-center text-xs font-bold text-primary hover:bg-surface-light border-t border-border transition">
                Ver auditoria completa
            </a>
        </div>
    </div>
</div>
@endsection
