@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('surgery-kits.show', $surgeryKit) }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Resultado Pós-Cirúrgico</h1>
            <p class="text-text-secondary text-sm">{{ $surgeryKit->kitTemplate->nome }} — {{ $surgeryKit->surgery->paciente }}</p>
        </div>
    </div>

    <form action="{{ route('surgery-kits.registrar-resultados', $surgeryKit) }}" method="POST" class="flex flex-col gap-6">
        @csrf

        @foreach([['Essenciais', $surgeryKit->essenciais], ['Sobressalentes', $surgeryKit->sobressalentes]] as [$grupo, $items])
        @if($items->count() > 0)
        <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-surface font-bold text-text-primary">{{ $grupo }}</div>
            <div class="divide-y divide-border">
                @foreach($items as $item)
                <div class="p-5 grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <div>
                        <p class="font-semibold text-text-primary text-sm">{{ $item->kitTemplateItem->productTemplate->nome }}</p>
                        @if($item->stockItem)
                            <p class="text-xs text-text-secondary">Lote: {{ $item->stockItem->lote ?? '—' }} | SN: {{ $item->stockItem->numero_serie ?? '—' }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-bold text-text-secondary uppercase">Resultado</label>
                        <select name="itens[{{ $item->id }}][resultado]" required class="mt-1 w-full bg-surface border border-border rounded-xl px-3 py-2 text-sm outline-none">
                            <option value="implantado_usado"  @selected($item->resultado === 'implantado_usado')>Implantado/Usado</option>
                            <option value="consumido"         @selected($item->resultado === 'consumido')>Consumido</option>
                            <option value="devolvido_intacto" @selected($item->resultado === 'devolvido_intacto')>Devolvido Intacto</option>
                            <option value="descartado"        @selected($item->resultado === 'descartado')>Descartado</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-text-secondary uppercase">Motivo (descarte)</label>
                        <select name="itens[{{ $item->id }}][motivo_descarte]" class="mt-1 w-full bg-surface border border-border rounded-xl px-3 py-2 text-sm outline-none">
                            <option value="">—</option>
                            <option value="contaminacao">Contaminação</option>
                            <option value="queda">Queda</option>
                            <option value="quebra">Quebra</option>
                            <option value="necessidade_tecnica">Necessidade Técnica</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-text-secondary uppercase">
                            Observação {{ $grupo === 'Sobressalentes' ? '(obrigatória se usado)' : '' }}
                        </label>
                        <input type="text" name="itens[{{ $item->id }}][observacao_resultado]"
                            value="{{ $item->observacao_resultado }}"
                            placeholder="{{ $grupo === 'Sobressalentes' ? 'Justificativa obrigatória...' : 'Observação...' }}"
                            class="mt-1 w-full bg-surface border border-border rounded-xl px-3 py-2 text-sm outline-none">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        <div class="flex gap-4">
            <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                Registrar Resultados
            </button>
            <a href="{{ route('surgery-kits.show', $surgeryKit) }}" class="px-8 py-3 bg-white border border-border text-text-secondary rounded-2xl font-bold hover:bg-surface transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
