@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('stock.index') }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-text-primary">Novo Item de Estoque</h1>
    </div>

    <form action="{{ route('stock.store') }}" method="POST" class="bg-white rounded-2xl border border-border p-8 shadow-sm flex flex-col gap-6">
        @csrf
        <div class="flex flex-col gap-1">
            <label class="text-sm font-bold text-text-secondary">Produto (Catálogo)</label>
            <select name="product_template_id" required class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                <option value="">Selecione o produto...</option>
                @foreach($products->groupBy('fabricante') as $fab => $prods)
                <optgroup label="{{ $fab }}">
                    @foreach($prods as $p)
                    <option value="{{ $p->id }}" @selected(old('product_template_id') == $p->id)>
                        {{ $p->nome }} ({{ $p->codigo }}) — {{ strtoupper($p->tipo) }}
                    </option>
                    @endforeach
                </optgroup>
                @endforeach
            </select>
            @error('product_template_id') <p class="text-xs text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Lote</label>
                <input type="text" name="lote" value="{{ old('lote') }}" placeholder="Ex: LOT-2025-001"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Número de Série</label>
                <input type="text" name="numero_serie" value="{{ old('numero_serie') }}" placeholder="Ex: SN-ABC-001"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                @error('numero_serie') <p class="text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Validade</label>
                <input type="date" name="validade" value="{{ old('validade') }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Tamanho</label>
                <input type="text" name="tamanho" value="{{ old('tamanho') }}" placeholder="Ex: 44, M, 10mm"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Referência do Fabricante</label>
                <input type="text" name="referencia_fabricante" value="{{ old('referencia_fabricante') }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Quantidade</label>
                <input type="number" name="quantidade" value="{{ old('quantidade', 1) }}" min="1"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                Cadastrar Item
            </button>
            <a href="{{ route('stock.index') }}" class="px-8 py-3 bg-white border border-border text-text-secondary rounded-2xl font-bold hover:bg-surface transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
