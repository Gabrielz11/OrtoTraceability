@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('stock.show', $stock) }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-text-primary">Editar Item de Estoque</h1>
    </div>

    <form action="{{ route('stock.update', $stock) }}" method="POST" class="bg-white rounded-2xl border border-border p-8 shadow-sm flex flex-col gap-6">
        @csrf @method('PUT')
        <div class="p-3 bg-surface rounded-xl text-sm text-text-secondary">
            Produto: <strong>{{ $stock->productTemplate->nome }}</strong> ({{ $stock->productTemplate->fabricante }})
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Lote</label>
                <input type="text" name="lote" value="{{ old('lote', $stock->lote) }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Número de Série</label>
                <input type="text" name="numero_serie" value="{{ old('numero_serie', $stock->numero_serie) }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm outline-none">
                @error('numero_serie') <p class="text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Validade</label>
                <input type="date" name="validade" value="{{ old('validade', $stock->validade?->format('Y-m-d')) }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Tamanho</label>
                <input type="text" name="tamanho" value="{{ old('tamanho', $stock->tamanho) }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm outline-none">
            </div>
            <div class="col-span-2 flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Referência do Fabricante</label>
                <input type="text" name="referencia_fabricante" value="{{ old('referencia_fabricante', $stock->referencia_fabricante) }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm outline-none">
            </div>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                Salvar
            </button>
            <a href="{{ route('stock.show', $stock) }}" class="px-8 py-3 bg-white border border-border text-text-secondary rounded-2xl font-bold hover:bg-surface transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
