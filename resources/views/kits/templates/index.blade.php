@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Templates de Kit</h1>
            <p class="text-text-secondary mt-1">Definições de kits por fabricante e procedimento.</p>
        </div>
        <a href="{{ route('kit-templates.create') }}" class="px-5 py-2.5 bg-primary text-white rounded-2xl font-semibold shadow-sm hover:shadow-md transition">
            + Novo Template
        </a>
    </div>

    @if($insufficientKits->isNotEmpty())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
        <h3 class="font-bold text-danger mb-2">Kits com Estoque Insuficiente</h3>
        <ul class="text-sm text-danger space-y-1">
            @foreach($insufficientKits as $alert)
            <li>• <strong>{{ $alert['kit']->nome }}</strong> — faltam: {{ $alert['itens_faltando']->map(fn($i) => $i->productTemplate->nome)->join(', ') }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        @php
            $podeMontar = $template->podeSerMontado();
            $tipoColors = ['implante' => 'bg-blue-100 text-primary', 'instrumental' => 'bg-purple-100 text-purple-700', 'consumivel' => 'bg-green-100 text-success'];
        @endphp
        <div class="bg-white rounded-2xl border border-border shadow-sm p-6 flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $tipoColors[$template->tipo_kit] ?? 'bg-surface text-text-secondary' }}">
                        {{ strtoupper($template->tipo_kit) }}
                    </span>
                    <h3 class="font-bold text-text-primary mt-2">{{ $template->nome }}</h3>
                    <p class="text-xs text-text-secondary">{{ $template->fabricante }} • {{ $template->procedimento }}</p>
                </div>
                <span class="w-3 h-3 rounded-full mt-1 {{ $podeMontar ? 'bg-success' : 'bg-danger' }}" title="{{ $podeMontar ? 'Estoque OK' : 'Estoque insuficiente' }}"></span>
            </div>

            <div class="text-xs text-text-secondary">
                <span class="font-semibold">{{ $template->items->count() }}</span> itens no template
                ({{ $template->items->where('criticidade', 'essencial')->count() }} essenciais)
            </div>

            <div class="flex gap-2 mt-auto pt-4 border-t border-border">
                <a href="{{ route('kit-templates.show', $template) }}" class="flex-1 text-center py-2 text-sm font-semibold text-primary hover:bg-primary-light rounded-xl transition">
                    Ver Template
                </a>
                <form action="{{ route('kit-templates.destroy', $template) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Desativar este template?')"
                        class="px-4 py-2 text-sm text-text-secondary hover:text-danger transition">
                        Desativar
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-text-secondary italic">Nenhum template cadastrado.</div>
        @endforelse
    </div>
</div>
@endsection
