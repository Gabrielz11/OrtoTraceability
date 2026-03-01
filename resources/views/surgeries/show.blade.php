@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8" x-data="{ tab: 'materials' }">
    <div class="flex items-center justify-between">
        <div class="flex items-baseline gap-4">
            <h1 class="text-3xl font-bold text-text-primary">Cirurgia de {{ $surgery->paciente }}</h1>
            <span class="text-xl font-semibold text-text-secondary">{{ $surgery->data_hora->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('surgeries.edit', $surgery) }}" class="px-5 py-2.5 bg-white border border-border text-text-primary rounded-2xl font-semibold shadow-sm hover:bg-surface transition">
                Editar Procedimento
            </a>
            <form action="{{ route('surgeries.destroy', $surgery) }}" method="POST" onsubmit="return confirm('Tem certeza? Isso marcará a cirurgia como deletada (soft delete).')">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 bg-red-50 text-danger border border-red-100 rounded-2xl font-semibold shadow-sm hover:bg-red-100 transition">
                    Remover
                </button>
            </form>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="p-6 rounded-2xl flex items-center justify-between border-2 {{ $surgery->status == 'realizada' ? 'bg-green-50 border-green-200 text-success' : ($surgery->status == 'cancelada' ? 'bg-red-50 border-red-200 text-danger' : 'bg-blue-50 border-blue-200 text-primary') }}">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg bg-white shadow-sm">
                {{ strtoupper(substr($surgery->status, 0, 1)) }}
            </div>
            <div>
                <h4 class="font-bold text-lg">Procedimento {{ ucfirst($surgery->status) }}</h4>
                <p class="text-sm opacity-80">{{ $surgery->hospital }} • Dr(a). {{ $surgery->medico }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <!-- CTAs for status update could go here -->
        </div>
    </div>

    <!-- Tab Bar -->
    <div class="flex border-b border-border">
        <button @click="tab = 'materials'" :class="tab === 'materials' ? 'border-primary text-primary' : 'border-transparent text-text-secondary'" class="px-6 py-3 font-semibold border-b-2 transition">Materiais Vinculados</button>
        <button @click="tab = 'history'" :class="tab === 'history' ? 'border-primary text-primary' : 'border-transparent text-text-secondary'" class="px-6 py-3 font-semibold border-b-2 transition">Histórico Procedimento</button>
    </div>

    <!-- Materials Tab -->
    <div x-show="tab === 'materials'" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Linked Materials List -->
        <div class="lg:col-span-3 bg-white rounded-2xl border border-border overflow-hidden shadow-sm">
            <h3 class="px-6 py-4 border-b border-border font-bold text-text-primary bg-surface">Materiais na Cirurgia</h3>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-text-secondary bg-surface-light border-b border-border">
                        <th class="px-6 py-3 font-semibold">Lote / Série</th>
                        <th class="px-6 py-3 font-semibold text-center">Ação</th>
                        <th class="px-6 py-3 font-semibold text-right">Gerenciar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($surgery->materials as $material)
                    <tr class="hover:bg-surface-light transition">
                        <td class="px-6 py-4">
                            <p class="font-bold text-text-primary underline hover:text-primary"><a href="{{ route('materials.show', $material) }}">{{ $material->lote }}</a></p>
                            <p class="text-[10px] text-text-secondary">{{ $material->numero_serie }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold border {{ $material->pivot->acao == 'usado' ? 'bg-green-100 text-success border-success/20' : 'bg-blue-100 text-primary border-primary/20' }}">
                                {{ $material->pivot->acao == 'usado' ? 'IMPLANTADO/USADO' : 'RESERVADO' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex gap-2 justify-end">
                                @if($material->pivot->acao == 'reservado' && $surgery->status != 'cancelada')
                                <form action="{{ route('surgeries.use', [$surgery, $material]) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1.5 bg-success text-white rounded-lg text-[10px] font-bold hover:bg-green-700 transition">Confirmar Uso</button>
                                </form>
                                <form action="{{ route('surgeries.unlink', [$surgery, $material]) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 bg-white border border-border text-text-secondary rounded-lg text-[10px] font-bold hover:bg-red-50 hover:text-danger hover:border-danger transition">Remover</button>
                                </form>
                                @elseif($material->pivot->acao == 'usado')
                                <span class="text-[10px] text-text-secondary italic">Imutável após uso</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-text-secondary italic font-medium">Nenhum material vinculado a este procedimento.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Material Link Form -->
        <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm flex flex-col gap-6">
            <h3 class="font-bold text-text-primary">Adicionar Material</h3>
            <p class="text-xs text-text-secondary">Selecione materiais disponíveis em estoque para vincular à cirurgia.</p>
            
            <form action="{{ route('surgeries.link', $surgery) }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-text-secondary uppercase">Busca rápida / Lote</label>
                    <select name="material_id" required class="w-full bg-white border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary outline-none">
                        <option value="">Selecione...</option>
                        @foreach($available_materials as $item)
                        <option value="{{ $item->id }}" {{ $item->isExpired() ? 'disabled' : '' }}>
                            {{ $item->lote }} (Exp: {{ $item->validade->format('d/m/y') }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition">Vincular Material</button>
            </form>

            @if($available_materials->where('status', 'em_estoque')->count() == 0)
                <p class="text-[10px] text-danger text-center bg-red-50 p-3 rounded-lg border border-red-100 italic">Estoque baixo ou zerado.</p>
            @endif
        </div>
    </div>

    <!-- History Tab -->
    <div x-show="tab === 'history'" class="bg-white rounded-2xl border border-border p-8 shadow-sm">
        <h3 class="text-lg font-bold text-text-primary mb-6">Auditoria do Procedimento</h3>
        <div class="divide-y divide-border">
            @forelse($audits as $audit)
            <div class="py-4 flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm bg-primary-light text-primary px-2 py-0.5 rounded uppercase tracking-wider">{{ $audit->action }}</span>
                    <span class="text-xs text-text-secondary">{{ $audit->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <p class="text-xs text-text-secondary">Executor: <span class="font-bold">{{ $audit->user?->name ?? 'Sistema' }}</span></p>
                <div class="mt-2 text-[10px] font-mono p-3 bg-surface border border-border rounded-lg opacity-80">
                    {{ json_encode($audit->after ?? $audit->metadata) }}
                </div>
            </div>
            @empty
            <p class="py-8 text-center text-text-secondary italic">Ainda não há registros relevantes.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
