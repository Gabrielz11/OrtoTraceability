@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8" x-data="{ tab: 'info' }">
    <div class="flex items-center justify-between">
        <div class="flex items-baseline gap-4">
            <h1 class="text-3xl font-bold text-text-primary">Material #{{ $material->id }}</h1>
            <span class="text-2xl font-semibold text-text-secondary">{{ $material->lote }}</span>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('materials.edit', $material) }}" class="px-5 py-2.5 bg-white border border-border text-text-primary rounded-2xl font-semibold shadow-sm hover:bg-surface transition">
                Editar
            </a>
            <form action="{{ route('materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Tem certeza? Isso marcará o item como deletado (soft delete).')">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 bg-red-50 text-danger border border-red-100 rounded-2xl font-semibold shadow-sm hover:bg-red-100 transition">
                    Remover
                </button>
            </form>
        </div>
    </div>

    <!-- Tab Bar -->
    <div class="flex border-b border-border mb-4">
        <button @click="tab = 'info'" :class="tab === 'info' ? 'border-primary text-primary' : 'border-transparent text-text-secondary'" class="px-6 py-3 font-semibold border-b-2 transition">Informações Gerais</button>
        <button @click="tab = 'history'" :class="tab === 'history' ? 'border-primary text-primary' : 'border-transparent text-text-secondary'" class="px-6 py-3 font-semibold border-b-2 transition">Histórico / Auditoria</button>
    </div>

    <!-- Info Tab -->
    <div x-show="tab === 'info'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-border p-8 shadow-sm">
            <h3 class="text-lg font-bold text-text-primary mb-6">Ficha Técnica</h3>
            <div class="grid grid-cols-2 gap-y-8">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Fabricante</label>
                    <p class="text-text-primary font-medium">{{ $material->fabricante }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Situação / Status</label>
                    <span class="inline-block px-3 py-1 bg-surface border border-border rounded-full text-xs font-bold">{{ strtoupper(str_replace('_', ' ', $material->status)) }}</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Lote</label>
                    <p class="text-text-primary font-medium">{{ $material->lote }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Cód. / Série</label>
                    <p class="text-text-primary font-medium">{{ $material->numero_serie ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Vencimento</label>
                    <p class="text-text-primary font-medium {{ $material->isExpired() ? 'text-danger font-bold underline' : '' }}">
                        {{ $material->validade->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Cadastro</label>
                    <p class="text-text-secondary text-sm">{{ $material->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if($material->observacoes)
            <div class="mt-8 pt-8 border-t border-border">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Observações Internas</label>
                <p class="text-text-primary text-sm whitespace-pre-line">{{ $material->observacoes }}</p>
            </div>
            @endif
        </div>

        <div class="bg-surface rounded-2xl border border-border p-8 shadow-sm">
            <h3 class="text-lg font-bold text-text-primary mb-6">Vínculos de Cirurgia</h3>
            @php $current_surgery = $material->surgeries->last(); @endphp
            @if($current_surgery)
                <div class="p-4 bg-white border border-border rounded-xl shadow-sm mb-4">
                    <p class="text-xs font-bold text-primary uppercase mb-1">Cirurgia Ativa</p>
                    <p class="text-sm font-bold text-text-primary mb-2 line-clamp-1">{{ $current_surgery->paciente }}</p>
                    <div class="flex justify-between text-xs text-text-secondary">
                        <span>{{ $current_surgery->data_hora->format('d/m/Y') }}</span>
                        <span>{{ strtoupper($current_surgery->pivot->acao) }}</span>
                    </div>
                    <a href="{{ route('surgeries.show', $current_surgery) }}" class="mt-4 block text-center py-2 bg-primary text-white rounded-lg text-sm font-bold shadow-sm">Abrir Cirurgia</a>
                </div>
            @else
                <div class="py-12 text-center">
                    <svg class="w-12 h-12 text-border mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="text-sm text-text-secondary">Sem vínculos no momento.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- History Tab -->
    <div x-show="tab === 'history'" class="bg-white rounded-2xl border border-border p-8 shadow-sm">
        <h3 class="text-lg font-bold text-text-primary mb-6">Linha do Tempo de Auditoria</h3>
        <div class="flex flex-col gap-6 relative before:absolute before:left-3.5 before:top-4 before:bottom-4 before:w-0.5 before:bg-border">
            @forelse($audits as $audit)
            <div class="flex gap-6 relative">
                <div class="w-8 h-8 rounded-full bg-white border-4 border-primary z-10 flex-shrink-0"></div>
                <div>
                    <p class="font-bold text-text-primary text-sm">{{ ucfirst($audit->action) }}</p>
                    <p class="text-xs text-text-secondary">{{ $audit->created_at->format('d/m/Y H:i') }} • {{ $audit->user?->name ?? 'Sistema' }}</p>
                    @if($audit->before || $audit->after)
                    <div class="mt-3 p-3 bg-surface rounded-lg border border-border text-xs font-mono space-y-2 max-w-xl">
                        @if($audit->before)
                        <div class="text-red-500">- {{ json_encode($audit->before) }}</div>
                        @endif
                        @if($audit->after)
                        <div class="text-green-600">+ {{ json_encode($audit->after) }}</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-center text-text-secondary italic">Sem histórico disponível.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
