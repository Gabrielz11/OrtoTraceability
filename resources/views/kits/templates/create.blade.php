@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('kit-templates.index') }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-3xl font-bold text-text-primary">Novo Template de Kit</h1>
    </div>

    <form action="{{ route('kit-templates.store') }}" method="POST" class="bg-white rounded-2xl border border-border p-8 shadow-sm flex flex-col gap-6">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Nome do Template</label>
                <input type="text" name="nome" value="{{ old('nome') }}" required placeholder="Ex: Kit Prótese Quadril Medacta — Implantes"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                @error('nome') <p class="text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Fabricante</label>
                <input type="text" name="fabricante" value="{{ old('fabricante') }}" required placeholder="Ex: Medacta"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                @error('fabricante') <p class="text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Tipo do Kit</label>
                <select name="tipo_kit" required class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                    <option value="implante"      @selected(old('tipo_kit') === 'implante')>Implante</option>
                    <option value="instrumental"  @selected(old('tipo_kit') === 'instrumental')>Instrumental</option>
                    <option value="consumivel"    @selected(old('tipo_kit') === 'consumivel')>Consumível</option>
                </select>
            </div>
            <div class="col-span-2 flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Procedimento</label>
                <input type="text" name="procedimento" value="{{ old('procedimento') }}" required placeholder="Ex: Artroplastia Total de Quadril"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="col-span-2 flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Descrição (opcional)</label>
                <textarea name="descricao" rows="3" class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none resize-none">{{ old('descricao') }}</textarea>
            </div>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                Criar Template
            </button>
            <a href="{{ route('kit-templates.index') }}" class="px-6 py-3 bg-white border border-border text-text-secondary rounded-2xl font-bold hover:bg-surface transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
