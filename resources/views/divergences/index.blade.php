@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Divergências</h1>
            <p class="text-text-secondary mt-1">Alertas de não-conformidade detectados pelo motor de validação.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Severidade</label>
            <select name="severity" class="border border-border rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="">Todas</option>
                <option value="critical" @selected(request('severity') === 'critical')>Crítica</option>
                <option value="warning"  @selected(request('severity') === 'warning')>Aviso</option>
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Status</label>
            <select name="status" class="border border-border rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="">Todos</option>
                <option value="open"         @selected(request('status') === 'open')>Aberta</option>
                <option value="acknowledged" @selected(request('status') === 'acknowledged')>Reconhecida</option>
                <option value="resolved"     @selected(request('status') === 'resolved')>Resolvida</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90 transition">
            Filtrar
        </button>
        @if(request('severity') || request('status'))
            <a href="{{ route('divergences.index') }}" class="px-4 py-2 text-sm text-text-secondary hover:text-danger transition">
                Limpar
            </a>
        @endif
    </form>

    {{-- Tabela --}}
    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface border-b border-border text-text-secondary">
                <tr>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Material</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Cirurgia</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Regra</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Severidade</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Mensagem</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Ocorrido em</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Ação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($divergences as $div)
                <tr class="hover:bg-surface transition {{ $div->severity === 'critical' && $div->isOpen() ? 'bg-red-50/30' : '' }}">
                    <td class="px-6 py-4">
                        @if($div->material)
                            <p class="font-semibold text-text-primary">{{ $div->material->nome }}</p>
                            <p class="text-xs text-text-secondary">Lote: {{ $div->material->lote }}</p>
                        @else
                            <span class="text-text-secondary italic">Material removido</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-text-secondary text-xs">
                        @if($div->surgery)
                            <p class="font-medium text-text-primary">{{ $div->surgery->hospital }}</p>
                            <p>{{ $div->surgery->data_hora->format('d/m/Y') }}</p>
                        @else
                            <span class="italic">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs text-text-secondary">{{ $div->rule_name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($div->severity === 'critical')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-danger">Crítica</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-warning">Aviso</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-text-secondary max-w-xs">
                        <p class="text-sm">{{ $div->message }}</p>
                    </td>
                    <td class="px-6 py-4 text-text-secondary whitespace-nowrap text-xs">
                        {{ $div->occurred_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($div->status === 'open')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Aberta</span>
                        @elseif($div->status === 'acknowledged')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Reconhecida</span>
                            @if($div->acknowledgedBy)
                                <p class="text-[10px] text-text-secondary mt-0.5">por {{ $div->acknowledgedBy->name }}</p>
                            @endif
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-success">Resolvida</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($div->isOpen())
                            <form method="POST" action="{{ route('divergences.acknowledge', $div) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-medium text-primary hover:underline"
                                    onclick="return confirm('Reconhecer esta divergência?')">
                                    Reconhecer
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-text-secondary">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-text-secondary italic">
                        Nenhuma divergência encontrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($divergences->hasPages())
        <div class="px-6 py-4 border-t border-border bg-surface">
            {{ $divergences->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
