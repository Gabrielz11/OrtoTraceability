@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('stock.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Estoque</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-xl font-semibold text-gray-800">
            Entrada de Estoque — {{ $product->nome }}
        </h1>
    </div>

    <div class="max-w-xl">

        {{-- Info do produto --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
            <p class="text-sm font-medium text-blue-800">{{ $product->nome }}</p>
            <p class="text-xs text-blue-600 mt-0.5">
                {{ $product->fabricante }} · {{ $product->tipoLabel() }} · {{ $product->categoriaLabel() }}
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <form method="POST"
                  action="{{ route('stock.items.store', $product) }}"
                  class="space-y-5">
                @csrf

                @if($product->requer_lote)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Lote <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="lote" value="{{ old('lote') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('lote') border-red-400 @enderror">
                    @error('lote')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                @if($product->requer_numero_serie)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Número de série <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="numero_serie" value="{{ old('numero_serie') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('numero_serie') border-red-400 @enderror">
                    @error('numero_serie')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Validade
                        </label>
                        <input type="date" name="validade" value="{{ old('validade') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tamanho / Referência
                        </label>
                        <input type="text" name="tamanho" value="{{ old('tamanho') }}"
                               placeholder="Ex: 52mm, M, 44"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantidade" value="{{ old('quantidade', 1) }}"
                           min="1" max="9999" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @if($product->tipo === 'consumivel')
                    <p class="text-xs text-gray-400 mt-1">Para consumíveis, informe a quantidade da caixa.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('stock.index') }}"
                       class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg
                              hover:bg-gray-100 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="bg-primary hover:bg-blue-700 text-white text-sm
                                   font-medium px-6 py-2 rounded-lg transition">
                        Registrar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
