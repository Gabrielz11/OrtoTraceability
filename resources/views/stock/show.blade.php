@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('stock.index') }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-text-primary">{{ $stock->productTemplate->nome }}</h1>
                <p class="text-text-secondary text-sm">{{ $stock->productTemplate->fabricante }} • {{ $stock->productTemplate->codigo }}</p>
            </div>
        </div>
        @if($stock->status === 'em_estoque')
        <div class="flex gap-2">
            <a href="{{ route('stock.edit', $stock) }}" class="px-4 py-2 bg-white border border-border text-text-secondary rounded-xl text-sm font-bold hover:bg-surface transition">
                Editar
            </a>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-border p-8 shadow-sm">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Status</label>
                    @php
                        $sc = ['em_estoque'=>'bg-green-100 text-success','reservado'=>'bg-yellow-100 text-warning','despachado'=>'bg-purple-100 text-purple-700','descartado'=>'bg-red-100 text-danger','devolvido'=>'bg-blue-100 text-primary'];
                    @endphp
                    <p class="mt-1"><span class="px-3 py-1 rounded-full text-sm font-bold {{ $sc[$stock->status] ?? 'bg-surface text-text-secondary' }}">{{ str_replace('_',' ',ucfirst($stock->status)) }}</span></p>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Tipo</label>
                    <p class="mt-1 text-text-primary font-medium">{{ strtoupper(str_replace('_',' ',$stock->productTemplate->tipo)) }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Lote</label>
                    <p class="mt-1 text-text-primary font-medium font-mono">{{ $stock->lote ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Número de Série</label>
                    <p class="mt-1 text-text-primary font-medium font-mono">{{ $stock->numero_serie ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Validade</label>
                    <p class="mt-1 {{ $stock->isExpired() ? 'text-danger font-bold' : ($stock->isNearExpiry() ? 'text-warning font-semibold' : 'text-text-primary font-medium') }}">
                        {{ $stock->validade?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Tamanho</label>
                    <p class="mt-1 text-text-primary font-medium">{{ $stock->tamanho ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Ref. Fabricante</label>
                    <p class="mt-1 text-text-primary font-medium">{{ $stock->referencia_fabricante ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Quantidade</label>
                    <p class="mt-1 text-text-primary font-bold text-lg">{{ $stock->quantidade }}</p>
                </div>
            </div>
        </div>

        @if($stock->status === 'em_estoque')
        <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm flex flex-col gap-4">
            <h3 class="font-bold text-text-primary">Descartar Item</h3>
            <form action="{{ route('stock.discard', $stock) }}" method="POST" class="flex flex-col gap-3">
                @csrf
                <select name="motivo" required class="w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
                    <option value="">Motivo do descarte...</option>
                    <option value="contaminacao">Contaminação</option>
                    <option value="queda">Queda</option>
                    <option value="quebra">Quebra</option>
                    <option value="uso_incorreto">Uso Incorreto</option>
                    <option value="vencimento">Vencimento</option>
                    <option value="outro">Outro</option>
                </select>
                <textarea name="observacao" rows="2" placeholder="Observação..." class="w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none resize-none"></textarea>
                <button type="submit" onclick="return confirm('Confirmar descarte?')" class="w-full py-2.5 bg-danger text-white rounded-xl text-sm font-bold hover:bg-red-700 transition">
                    Confirmar Descarte
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
