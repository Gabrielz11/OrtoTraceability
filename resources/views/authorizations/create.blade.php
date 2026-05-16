@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('surgeries.show', $surgery) }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Cadastrar Autorização</h1>
            <p class="text-text-secondary text-sm">{{ $surgery->paciente }} — {{ $surgery->data_hora->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <form action="{{ route('authorizations.store', $surgery) }}" method="POST" class="bg-white rounded-2xl border border-border p-8 shadow-sm flex flex-col gap-6">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Plano de Saúde</label>
                <input type="text" name="plano_saude" value="{{ old('plano_saude') }}" required placeholder="Ex: Unimed, Bradesco, SulAmérica..."
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                @error('plano_saude') <p class="text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Status</label>
                <select name="status" required class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                    <option value="nao_recebida" @selected(old('status','nao_recebida')==='nao_recebida')>Não Recebida</option>
                    <option value="recebida"     @selected(old('status')==='recebida')>Recebida</option>
                    <option value="parcial"      @selected(old('status')==='parcial')>Parcial</option>
                    <option value="vencida"      @selected(old('status')==='vencida')>Vencida</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Código da Autorização</label>
                <input type="text" name="codigo_autorizacao" value="{{ old('codigo_autorizacao') }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Data da Autorização</label>
                <input type="date" name="data_autorizacao" value="{{ old('data_autorizacao') }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Validade da Autorização</label>
                <input type="date" name="validade_autorizacao" value="{{ old('validade_autorizacao') }}"
                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
            </div>
            <div class="col-span-2 flex flex-col gap-1">
                <label class="text-sm font-bold text-text-secondary">Observações</label>
                <textarea name="observacoes" rows="3" class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/30 outline-none resize-none">{{ old('observacoes') }}</textarea>
            </div>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                Salvar Autorização
            </button>
            <a href="{{ route('surgeries.show', $surgery) }}" class="px-8 py-3 bg-white border border-border text-text-secondary rounded-2xl font-bold hover:bg-surface transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
