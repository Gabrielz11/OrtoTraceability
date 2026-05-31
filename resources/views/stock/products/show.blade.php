@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('stock.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Estoque</a>
            <span class="text-gray-300">/</span>
            <h1 class="text-xl font-semibold text-gray-800">{{ $product->nome }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('stock.items.create', $product) }}"
               class="text-sm bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                + Entrada de Estoque
            </a>
            <a href="{{ route('stock.products.edit', $product) }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                Editar
            </a>
        </div>
    </div>

    {{-- Dados do produto --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-2xl">
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Código</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $product->codigo }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">ANVISA</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $product->codigo_anvisa ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Fabricante</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $product->fabricante }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tipo</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $product->tipoLabel() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Categoria</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $product->categoriaLabel() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Em estoque</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $product->quantidadeDisponivel() }} un.</dd>
            </div>
        </dl>
    </div>

    {{-- Itens agrupados por status --}}
    @foreach($grouped as $status => $items)
    <div>
        <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">
            {{ str_replace('_', ' ', $status) }} ({{ $items->count() }})
        </h2>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-100">
            @foreach($items as $item)
            <div class="flex items-center gap-4 px-4 py-3 text-sm">
                <span class="font-mono text-xs text-gray-500 w-32">
                    {{ $item->lote ? 'Lote ' . $item->lote : '—' }}
                </span>
                @if($item->numero_serie)
                <span class="text-xs text-gray-400">Série {{ $item->numero_serie }}</span>
                @endif
                @if($item->tamanho)
                <span class="text-xs text-gray-500">{{ $item->tamanho }}</span>
                @endif
                @if($item->validade)
                <span class="text-xs px-2 py-0.5 rounded-full {{ $item->expiryBadgeClass() }}">
                    {{ $item->expiryLabel() }}
                </span>
                @endif
                <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ $item->statusBadgeClass() }}">
                    {{ str_replace('_', ' ', $item->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($product->stockItems->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 text-sm">
        Nenhum item cadastrado para este produto.
    </div>
    @endif

</div>
@endsection
