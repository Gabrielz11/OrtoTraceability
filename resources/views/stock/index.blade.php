@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-text-primary">Estoque</h1>
        <a href="{{ route('stock.products.create') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-blue-700
                  text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            + Cadastrar Produto
        </a>
    </div>

    {{-- Alertas de estoque --}}
    @if($alerts['vencidos'] > 0 || $alerts['near_expiry'] > 0 || $alerts['sem_estoque'] > 0)
    <div class="flex flex-wrap gap-3">
        @if($alerts['vencidos'] > 0)
        <div class="flex items-center gap-2 bg-red-50 border border-red-200
                    text-red-700 text-sm px-4 py-2 rounded-lg">
            🔴 <strong>{{ $alerts['vencidos'] }}</strong> item(ns) vencido(s) em estoque
        </div>
        @endif
        @if($alerts['near_expiry'] > 0)
        <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-200
                    text-yellow-700 text-sm px-4 py-2 rounded-lg">
            ⚠️ <strong>{{ $alerts['near_expiry'] }}</strong> item(ns) vencendo em 30 dias
        </div>
        @endif
        @if($alerts['sem_estoque'] > 0)
        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200
                    text-gray-600 text-sm px-4 py-2 rounded-lg">
            📦 <strong>{{ $alerts['sem_estoque'] }}</strong> produto(s) sem estoque
        </div>
        @endif
    </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('stock.index') }}"
          class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por nome, fabricante ou código..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <select name="tipo"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos os tipos</option>
                @foreach($tipos as $value => $label)
                <option value="{{ $value }}" @selected(request('tipo') === $value)>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="categoria"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas as categorias</option>
                @foreach($categorias as $value => $label)
                <option value="{{ $value }}" @selected(request('categoria') === $value)>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700
                       text-sm px-4 py-2 rounded-lg transition">
            Filtrar
        </button>
        @if(request()->hasAny(['search', 'tipo', 'categoria']))
        <a href="{{ route('stock.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 py-2">
            Limpar
        </a>
        @endif
    </form>

    {{-- Flash message --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700
                text-sm px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- Lista de produtos --}}
    <div class="space-y-2">
        @forelse($products as $product)
        <div x-data="{ open: false }"
             class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">

            {{-- Cabeçalho do produto --}}
            <div class="flex items-center gap-4 p-4 cursor-pointer"
                 @click="open = !open">

                {{-- Ícone expansão --}}
                <span class="text-gray-400 text-xs w-4 shrink-0"
                      x-text="open ? '▼' : '▶'"></span>

                {{-- Info principal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-medium text-gray-900 text-sm">
                            {{ $product->nome }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $product->fabricante }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $product->tipo === 'implante_esteril' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $product->tipo === 'instrumental' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $product->tipo === 'consumivel' ? 'bg-teal-100 text-teal-700' : '' }}">
                            {{ $product->tipoLabel() }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $product->categoriaLabel() }}</span>
                        @if($product->codigo_anvisa)
                        <span class="text-xs text-gray-300">ANVISA: {{ $product->codigo_anvisa }}</span>
                        @endif
                    </div>
                </div>

                {{-- Quantidade e alertas --}}
                <div class="flex items-center gap-3 shrink-0">
                    @if($product->hasExpiredItems())
                    <span class="text-xs text-red-600 font-medium">🔴 vencido</span>
                    @elseif($product->hasNearExpiryItems())
                    <span class="text-xs text-yellow-600 font-medium">⚠️ a vencer</span>
                    @endif

                    @if($product->total_em_estoque === 0)
                    <span class="text-xs bg-red-50 border border-red-200
                                 text-red-600 px-2.5 py-1 rounded-full font-medium">
                        sem estoque
                    </span>
                    @else
                    <span class="text-xs bg-green-50 border border-green-200
                                 text-green-700 px-2.5 py-1 rounded-full font-medium">
                        {{ $product->total_em_estoque }} em estoque
                    </span>
                    @endif

                    {{-- Ações --}}
                    <div class="flex items-center gap-1" @click.stop>
                        <a href="{{ route('stock.items.create', $product) }}"
                           class="text-xs bg-primary hover:bg-blue-700 text-white
                                  px-3 py-1.5 rounded-lg transition font-medium">
                            + Entrada
                        </a>
                        <a href="{{ route('stock.products.edit', $product) }}"
                           class="text-xs text-gray-500 hover:text-gray-700
                                  px-2 py-1.5 rounded-lg hover:bg-gray-100 transition">
                            Editar
                        </a>
                    </div>
                </div>
            </div>

            {{-- Itens de estoque (expansível) --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="border-t border-gray-100">

                @forelse($product->stockItemsInStock as $item)
                <div class="flex items-center gap-4 px-6 py-2.5 text-sm
                            {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}
                            {{ $item->isExpired() ? '!bg-red-50' : '' }}
                            {{ $item->isNearExpiry() && !$item->isExpired() ? '!bg-yellow-50' : '' }}">

                    {{-- Lote --}}
                    <span class="font-mono text-xs text-gray-500 w-32 shrink-0">
                        {{ $item->lote ? 'Lote ' . $item->lote : '—' }}
                    </span>

                    {{-- Série --}}
                    @if($item->numero_serie)
                    <span class="text-xs text-gray-400 w-32 shrink-0 truncate">
                        Série {{ $item->numero_serie }}
                    </span>
                    @endif

                    {{-- Tamanho --}}
                    @if($item->tamanho)
                    <span class="text-xs text-gray-500">{{ $item->tamanho }}</span>
                    @endif

                    {{-- Quantidade (para consumíveis) --}}
                    @if($item->quantidade > 1)
                    <span class="text-xs text-gray-500">Qtd: {{ $item->quantidade }}</span>
                    @endif

                    {{-- Validade --}}
                    @if($item->validade)
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $item->expiryBadgeClass() }}">
                        {{ $item->expiryLabel() }}
                    </span>
                    @endif

                    {{-- Status --}}
                    <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ $item->statusBadgeClass() }}">
                        {{ str_replace('_', ' ', $item->status) }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-4 text-sm text-gray-400 italic">
                    Nenhum item em estoque no momento.
                </div>
                @endforelse
            </div>
        </div>
        @empty
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <p class="text-gray-400 text-sm">Nenhum produto encontrado.</p>
            <a href="{{ route('stock.products.create') }}"
               class="inline-block mt-3 text-sm text-blue-600 hover:underline">
                Cadastrar primeiro produto →
            </a>
        </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div>
        {{ $products->links() }}
    </div>

</div>
@endsection
