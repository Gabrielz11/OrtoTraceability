@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Estoque (Stock Items)</h1>
            <p class="text-text-secondary mt-1">Itens físicos vinculados ao catálogo de produtos.</p>
        </div>
        <a href="{{ route('stock.create') }}" class="px-5 py-2.5 bg-primary text-white rounded-2xl font-semibold shadow-sm hover:shadow-md transition">
            + Novo Item
        </a>
    </div>

    <form method="GET" class="bg-surface rounded-2xl border border-border p-5 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[160px]">
            <label class="text-xs font-bold text-text-secondary uppercase">Lote</label>
            <input type="text" name="lote" value="{{ request('lote') }}" placeholder="Buscar por lote..."
                class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="text-xs font-bold text-text-secondary uppercase">Fabricante</label>
            <select name="fabricante" class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
                <option value="">Todos</option>
                @foreach($fabricantes as $fab)
                <option value="{{ $fab }}" @selected(request('fabricante') === $fab)>{{ $fab }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="text-xs font-bold text-text-secondary uppercase">Tipo</label>
            <select name="tipo" class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
                <option value="">Todos</option>
                <option value="implante_esteril" @selected(request('tipo') === 'implante_esteril')>Implante</option>
                <option value="instrumental"     @selected(request('tipo') === 'instrumental')>Instrumental</option>
                <option value="consumivel"       @selected(request('tipo') === 'consumivel')>Consumível</option>
            </select>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="text-xs font-bold text-text-secondary uppercase">Status</label>
            <select name="status" class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
                <option value="">Todos</option>
                <option value="em_estoque"   @selected(request('status') === 'em_estoque')>Em Estoque</option>
                <option value="reservado"    @selected(request('status') === 'reservado')>Reservado</option>
                <option value="despachado"   @selected(request('status') === 'despachado')>Despachado</option>
                <option value="descartado"   @selected(request('status') === 'descartado')>Descartado</option>
                <option value="devolvido"    @selected(request('status') === 'devolvido')>Devolvido</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium">Filtrar</button>
        @if(request()->hasAny(['lote','fabricante','tipo','status']))
        <a href="{{ route('stock.index') }}" class="px-4 py-2 text-sm text-text-secondary hover:text-danger">Limpar</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface border-b border-border text-text-secondary">
                <tr>
                    <th class="px-5 py-4 text-left font-semibold text-[10px] uppercase">Produto</th>
                    <th class="px-5 py-4 text-left font-semibold text-[10px] uppercase">Lote / Série</th>
                    <th class="px-5 py-4 text-left font-semibold text-[10px] uppercase">Tamanho</th>
                    <th class="px-5 py-4 text-left font-semibold text-[10px] uppercase">Validade</th>
                    <th class="px-5 py-4 text-left font-semibold text-[10px] uppercase">Qtd</th>
                    <th class="px-5 py-4 text-left font-semibold text-[10px] uppercase">Status</th>
                    <th class="px-5 py-4 text-right font-semibold text-[10px] uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($stockItems as $item)
                @php
                    $statusColors = [
                        'em_estoque'   => 'bg-green-100 text-success',
                        'reservado'    => 'bg-yellow-100 text-warning',
                        'despachado'   => 'bg-purple-100 text-purple-700',
                        'implantado_usado' => 'bg-gray-100 text-gray-600',
                        'consumido'    => 'bg-gray-100 text-gray-500',
                        'descartado'   => 'bg-red-100 text-danger',
                        'devolvido'    => 'bg-blue-100 text-primary',
                    ];
                @endphp
                <tr class="hover:bg-surface transition {{ $item->isExpired() ? 'bg-red-50/20' : '' }}">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-text-primary">{{ $item->productTemplate->nome }}</p>
                        <p class="text-[10px] text-text-secondary">{{ $item->productTemplate->fabricante }} • {{ $item->productTemplate->codigo }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-text-secondary">{{ $item->lote ?? '—' }}</p>
                        <p class="text-[10px] text-text-secondary">{{ $item->numero_serie ?? '' }}</p>
                    </td>
                    <td class="px-5 py-3 text-text-secondary">{{ $item->tamanho ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @if($item->validade)
                        <span class="{{ $item->isExpired() ? 'text-danger font-bold' : ($item->isNearExpiry() ? 'text-warning font-semibold' : 'text-text-primary') }}">
                            {{ $item->validade->format('d/m/Y') }}
                        </span>
                        @else
                        <span class="text-text-secondary">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-text-secondary">{{ $item->quantidade }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$item->status] ?? 'bg-surface text-text-secondary' }}">
                            {{ str_replace('_', ' ', ucfirst($item->status)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('stock.show', $item) }}" class="text-primary text-xs font-bold hover:underline mr-2">Ver</a>
                        @if($item->status === 'em_estoque')
                        <a href="{{ route('stock.edit', $item) }}" class="text-text-secondary text-xs hover:underline mr-2">Editar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-text-secondary italic">Nenhum item encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($stockItems->hasPages())
        <div class="px-5 py-4 border-t border-border bg-surface">
            {{ $stockItems->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
