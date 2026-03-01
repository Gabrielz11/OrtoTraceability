@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Gestão de Cirurgias</h1>
            <p class="text-text-secondary mt-1">Acompanhamento de procedimentos e uso de materiais.</p>
        </div>
        <a href="{{ route('surgeries.create') }}" class="px-5 py-2.5 bg-primary text-white rounded-2xl font-semibold shadow-sm hover:shadow-md transition">
            + Agendar Cirurgia
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm">
        <form action="{{ route('surgeries.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-text-secondary mb-2">Filtrar por Status</label>
                <select name="status" class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                    <option value="">Todos</option>
                    <option value="agendada" {{ request('status') == 'agendada' ? 'selected' : '' }}>Agendada</option>
                    <option value="realizada" {{ request('status') == 'realizada' ? 'selected' : '' }}>Realizada</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-text-primary text-white rounded-xl font-semibold shadow-sm hover:bg-black transition">
                Filtrar
            </button>
            <a href="{{ route('surgeries.index') }}" class="px-5 py-2.5 bg-white border border-border text-text-secondary rounded-xl font-semibold shadow-sm hover:bg-surface transition">
                Limpar
            </a>
        </form>
    </div>

    <!-- Surgeries Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($surgeries as $surgery)
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md transition flex flex-col gap-6 relative overflow-hidden group">
            <!-- Ribbon -->
            <div class="absolute top-0 right-0 px-4 py-1.5 rounded-bl-2xl text-[10px] font-bold uppercase tracking-wider
                {{ $surgery->status == 'agendada' ? 'bg-blue-50 text-primary border-b border-l border-blue-100' : ($surgery->status == 'realizada' ? 'bg-green-50 text-success border-b border-l border-green-100' : 'bg-red-50 text-danger border-b border-l border-red-100') }}">
                {{ $surgery->status }}
            </div>

            <div>
                <p class="text-[10px] font-bold text-text-secondary uppercase tracking-widest mb-1">{{ $surgery->data_hora->diffForHumans() }}</p>
                <h3 class="text-xl font-bold text-text-primary line-clamp-1">{{ $surgery->paciente }}</h3>
                <p class="text-xs text-text-secondary mt-1">{{ $surgery->hospital }} • Dr(a). {{ $surgery->medico }}</p>
                
                @if($surgery->materials->count() > 0)
                <div class="mt-3 flex flex-wrap gap-1">
                    @foreach($surgery->materials->take(2) as $mat)
                        <span class="text-[9px] bg-surface border border-border px-1.5 py-0.5 rounded font-medium text-text-secondary">{{ $mat->nome }}</span>
                    @endforeach
                    @if($surgery->materials->count() > 2)
                        <span class="text-[9px] text-text-secondary font-bold">+{{ $surgery->materials->count() - 2 }} outros</span>
                    @endif
                </div>
                @endif
            </div>

            <div class="mt-auto border-t border-border pt-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-surface border border-border flex items-center justify-center text-xs font-bold text-text-primary">
                        {{ $surgery->materials->count() }}
                    </div>
                    <span class="text-xs text-text-secondary">Materiais</span>
                </div>
                <a href="{{ route('surgeries.show', $surgery) }}" class="px-4 py-2 bg-primary/5 text-primary text-sm font-bold rounded-xl group-hover:bg-primary group-hover:text-white transition">Gerenciar</a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-surface rounded-2xl border border-dashed border-border">
            <p class="text-text-secondary italic">Nenhuma cirurgia registrada.</p>
        </div>
        @endforelse
    </div>

    @if($surgeries->hasPages())
    <div class="mt-4">
        {{ $surgeries->links() }}
    </div>
    @endif
</div>
@endsection
