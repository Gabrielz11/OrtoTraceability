@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('materials.index') }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-3xl font-bold text-text-primary">Cadastrar Material</h1>
    </div>

    <form action="{{ route('materials.store') }}" method="POST" class="bg-white rounded-2xl border border-border p-10 shadow-sm flex flex-col gap-8">
        @csrf
        
        <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-secondary">Nome do Material (Obrigatório)</label>
            <input type="text" name="nome" value="{{ old('nome') }}" required placeholder="Ex: Placa de Titânio 3.5mm" class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
            @error('nome') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Lote (Obrigatório)</label>
                <input type="text" name="lote" value="{{ old('lote') }}" required placeholder="Ex: AX12-50" class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('lote') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Nº de Série (Opcional)</label>
                <input type="text" name="numero_serie" value="{{ old('numero_serie') }}" placeholder="Ex: SN987654" class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('numero_serie') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Data de Validade</label>
                <input type="date" name="validade" value="{{ old('validade') }}" required class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('validade') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Fabricante</label>
                <input type="text" name="fabricante" value="{{ old('fabricante') }}" required placeholder="Nome do fabricante" class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('fabricante') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-secondary">Status Inicial</label>
            <select name="status" class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                <option value="em_estoque" {{ old('status') == 'em_estoque' ? 'selected' : '' }}>Em Estoque</option>
                <option value="reservado" {{ old('status') == 'reservado' ? 'selected' : '' }}>Reservado</option>
                <option value="implantado_usado" {{ old('status') == 'implantado_usado' ? 'selected' : '' }}>Implantado/Usado</option>
                <option value="descartado" {{ old('status') == 'descartado' ? 'selected' : '' }}>Descartado</option>
                <option value="devolvido_ao_fornecedor" {{ old('status') == 'devolvido_ao_fornecedor' ? 'selected' : '' }}>Devolvido ao Fornecedor</option>
            </select>
            @error('status') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-secondary">Observações Adicionais</label>
            <textarea name="observacoes" rows="4" placeholder="Detalhes técnicos ou notas de auditoria..." class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">{{ old('observacoes') }}</textarea>
            @error('observacoes') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col gap-4 mt-4">
            <button type="submit" class="w-full py-4 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition text-lg">
                Salvar Cadastro de Material
            </button>
            <p class="text-xs text-center text-text-secondary italic">Todas as alterações de cadastro são registradas para auditoria.</p>
        </div>
    </form>
</div>
@endsection
