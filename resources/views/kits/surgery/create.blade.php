@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('surgeries.show', $surgery) }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Iniciar Kit para Cirurgia</h1>
            <p class="text-text-secondary text-sm">{{ $surgery->paciente }} — {{ $surgery->data_hora->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <form action="{{ route('surgery-kits.store', $surgery) }}" method="POST" class="bg-white rounded-2xl border border-border p-8 shadow-sm flex flex-col gap-6">
        @csrf
        <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-secondary">Template de Kit</label>
            <select name="kit_template_id" required class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                <option value="">Selecione o template...</option>
                @foreach($templates->groupBy('fabricante') as $fab => $kits)
                <optgroup label="{{ $fab }}">
                    @foreach($kits as $kit)
                    @php $podeMontar = $kit->podeSerMontado(); @endphp
                    <option value="{{ $kit->id }}" {{ !$podeMontar ? 'style=color:red' : '' }}>
                        {{ $kit->nome }} ({{ strtoupper($kit->tipo_kit) }})
                        {{ !$podeMontar ? '⚠ Estoque insuficiente' : '' }}
                    </option>
                    @endforeach
                </optgroup>
                @endforeach
            </select>
        </div>
        <p class="text-xs text-text-secondary bg-surface rounded-xl p-3">
            Ao iniciar, os slots para cada item do template serão criados. Você poderá então vincular os itens físicos do estoque a cada slot.
        </p>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                Iniciar Montagem
            </button>
            <a href="{{ route('surgeries.show', $surgery) }}" class="px-6 py-3 bg-white border border-border text-text-secondary rounded-2xl font-bold hover:bg-surface transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
