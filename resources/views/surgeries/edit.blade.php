@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('surgeries.show', $surgery) }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-3xl font-bold text-text-primary">Editar Cirurgia</h1>
    </div>

    <form action="{{ route('surgeries.update', $surgery) }}" method="POST" class="bg-white rounded-2xl border border-border p-10 shadow-sm flex flex-col gap-8">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Paciente (Nome Completo)</label>
                <input type="text" name="paciente" value="{{ old('paciente', $surgery->paciente) }}" required placeholder="Ex: João Silva..." class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('paciente') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Data e Hora</label>
                <input type="datetime-local" name="data_hora" value="{{ old('data_hora', $surgery->data_hora->format('Y-m-d\TH:i')) }}" required class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('data_hora') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Hospital</label>
                <input type="text" name="hospital" value="{{ old('hospital', $surgery->hospital) }}" required placeholder="Ex: Hospital Municipal..." class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('hospital') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-secondary">Médico Responsável</label>
                <input type="text" name="medico" value="{{ old('medico', $surgery->medico) }}" required placeholder="Ex: Dr. Fulano de Tal" class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                @error('medico') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-secondary">Status</label>
            <select name="status" class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                <option value="agendada"  @selected(old('status', $surgery->status) === 'agendada')>Agendada</option>
                <option value="realizada" @selected(old('status', $surgery->status) === 'realizada')>Realizada</option>
                <option value="cancelada" @selected(old('status', $surgery->status) === 'cancelada')>Cancelada</option>
            </select>
            @error('status') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-secondary">Notas / Recomendações OPME</label>
            <textarea name="observacoes" rows="4" placeholder="Detalhes técnicos para o procedimento..." class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">{{ old('observacoes', $surgery->observacoes) }}</textarea>
            @error('observacoes') <p class="text-xs text-danger font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4 mt-4">
            <button type="submit" class="flex-1 py-4 bg-primary text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition text-lg">
                Salvar Alterações
            </button>
            <a href="{{ route('surgeries.show', $surgery) }}" class="px-8 py-4 bg-white border border-border text-text-secondary rounded-2xl font-bold hover:bg-surface transition text-lg text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
