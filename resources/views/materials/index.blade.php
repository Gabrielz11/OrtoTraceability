@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Controle de Materiais</h1>
            <p class="text-text-secondary mt-1">Gerencie produtos e rastreabilidade unitária.</p>
        </div>
        <a href="{{ route('materials.create') }}" class="px-5 py-2.5 bg-primary text-white rounded-2xl font-semibold shadow-sm hover:shadow-md transition">
            + Cadastrar Material
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm">
        <form action="{{ route('materials.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-text-secondary mb-2">Filtrar por Nome</label>
                <input type="text" name="nome" value="{{ request('nome') }}" placeholder="Ex: Placa..." class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-text-secondary mb-2">Filtrar por Lote</label>
                <input type="text" name="lote" value="{{ request('lote') }}" placeholder="Ex: L123..." class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-text-secondary mb-2">Filtrar por Status</label>
                <select name="status" class="w-full bg-white border border-border rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                    <option value="">Todos</option>
                    <option value="em_estoque" {{ request('status') == 'em_estoque' ? 'selected' : '' }}>Em Estoque</option>
                    <option value="reservado" {{ request('status') == 'reservado' ? 'selected' : '' }}>Reservado</option>
                    <option value="implantado_usado" {{ request('status') == 'implantado_usado' ? 'selected' : '' }}>Implantado/Usado</option>
                    <option value="descartado" {{ request('status') == 'descartado' ? 'selected' : '' }}>Descartado</option>
                    <option value="devolvido_ao_fornecedor" {{ request('status') == 'devolvido_ao_fornecedor' ? 'selected' : '' }}>Devolvido</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-text-primary text-white rounded-xl font-semibold shadow-sm hover:bg-black transition">
                Filtrar
            </button>
            <a href="{{ route('materials.index') }}" class="px-5 py-2.5 bg-white border border-border text-text-secondary rounded-xl font-semibold shadow-sm hover:bg-surface transition">
                Limpar
            </a>
        </form>
    </div>

    <!-- Materials Table -->
    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface border-b border-border text-text-secondary">
                <tr>
                    <th class="px-6 py-4 font-semibold">Material</th>
                    <th class="px-6 py-4 font-semibold">Lote / Série</th>
                    <th class="px-6 py-4 font-semibold">Validade</th>
                    <th class="px-6 py-4 font-semibold">Fabricante</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($materials as $material)
                <tr class="hover:bg-surface-light transition {{ $material->isExpired() ? 'bg-red-50/20' : '' }}">
                    <td class="px-6 py-4">
                        <p class="font-bold text-text-primary">{{ $material->nome ?? 'Sem nome' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-text-secondary">{{ $material->lote }}</p>
                        <p class="text-xs text-text-secondary">{{ $material->numero_serie ?? 'Sem nº de série' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="{{ $material->isExpired() ? 'text-danger font-bold' : ($material->isNearExpiry() ? 'text-warning font-semibold' : 'text-text-primary') }}">
                            {{ $material->validade->format('d/m/Y') }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-text-secondary">{{ $material->fabricante }}</td>
                    <td class="px-6 py-4">
                        @php
                            $colors = [
                                'em_estoque' => 'bg-green-100 text-success border-success/20',
                                'reservado' => 'bg-blue-100 text-primary border-primary/20',
                                'implantado_usado' => 'bg-gray-100 text-gray-600 border-gray-200',
                                'descartado' => 'bg-red-100 text-danger border-danger/20',
                                'devolvido_ao_fornecedor' => 'bg-purple-100 text-purple-600 border-purple-200',
                            ];
                        @endphp
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $colors[$material->status] }}">
                            {{ strtoupper(str_replace('_', ' ', $material->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('materials.show', $material) }}" class="text-primary font-bold hover:underline mx-2">Detalhes</a>
                        <a href="{{ route('materials.edit', $material) }}" class="text-text-secondary hover:text-text-primary mx-2">Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-text-secondary italic">Nenhum material encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($materials->hasPages())
        <div class="px-6 py-4 border-t border-border bg-surface">
            {{ $materials->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
