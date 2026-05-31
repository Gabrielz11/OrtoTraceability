@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('stock.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Estoque</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-xl font-semibold text-gray-800">Cadastrar Produto</h1>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <form method="POST" action="{{ route('stock.products.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Código interno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      @error('codigo') border-red-400 @enderror">
                        @error('codigo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Registro ANVISA
                        </label>
                        <input type="text" name="codigo_anvisa" value="{{ old('codigo_anvisa') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nome do produto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nome" value="{{ old('nome') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('nome') border-red-400 @enderror">
                    @error('nome')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fabricante <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="fabricante" value="{{ old('fabricante') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecionar...</option>
                            <option value="implante_esteril" @selected(old('tipo') === 'implante_esteril')>Implante Estéril</option>
                            <option value="instrumental"     @selected(old('tipo') === 'instrumental')>Instrumental</option>
                            <option value="consumivel"       @selected(old('tipo') === 'consumivel')>Consumível</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Categoria <span class="text-red-500">*</span>
                        </label>
                        <select name="categoria" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecionar...</option>
                            <option value="protese_quadril"    @selected(old('categoria') === 'protese_quadril')>Prótese Quadril</option>
                            <option value="protese_joelho"     @selected(old('categoria') === 'protese_joelho')>Prótese Joelho</option>
                            <option value="protese_ombro"      @selected(old('categoria') === 'protese_ombro')>Prótese Ombro</option>
                            <option value="coluna"             @selected(old('categoria') === 'coluna')>Coluna</option>
                            <option value="trauma"             @selected(old('categoria') === 'trauma')>Trauma</option>
                            <option value="instrumental_geral" @selected(old('categoria') === 'instrumental_geral')>Instrumental Geral</option>
                            <option value="consumivel_geral"   @selected(old('categoria') === 'consumivel_geral')>Consumível Geral</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Unidade de medida
                        </label>
                        <input type="text" name="unidade_medida"
                               value="{{ old('unidade_medida', 'unidade') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2 flex items-end gap-6 pb-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="requer_lote" value="1"
                                   @checked(old('requer_lote', true))
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Requer lote</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="requer_numero_serie" value="1"
                                   @checked(old('requer_numero_serie'))
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Requer número de série</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Observações
                    </label>
                    <textarea name="observacoes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('observacoes') }}</textarea>
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
                        Cadastrar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
