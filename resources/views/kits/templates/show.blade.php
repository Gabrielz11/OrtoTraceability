@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('kit-templates.index') }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-text-primary">{{ $kitTemplate->nome }}</h1>
                <p class="text-text-secondary text-sm">{{ $kitTemplate->fabricante }} • {{ $kitTemplate->procedimento }}</p>
            </div>
        </div>
        @php
            $podeMontar = $kitTemplate->podeSerMontado();
        @endphp
        <span class="px-3 py-1.5 rounded-full text-sm font-bold {{ $podeMontar ? 'bg-green-100 text-success' : 'bg-red-100 text-danger' }}">
            {{ $podeMontar ? 'Estoque OK' : 'Estoque Insuficiente' }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Lista de itens do template --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-surface font-bold text-text-primary">
                Composição do Kit ({{ $kitTemplate->items->count() }} itens)
            </div>
            <table class="w-full text-sm">
                <thead class="bg-surface border-b border-border text-text-secondary">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-[10px] uppercase">Produto</th>
                        <th class="px-4 py-3 text-center font-semibold text-[10px] uppercase">Qtd Min.</th>
                        <th class="px-4 py-3 text-center font-semibold text-[10px] uppercase">Qtd Rec.</th>
                        <th class="px-4 py-3 text-center font-semibold text-[10px] uppercase">Criticidade</th>
                        <th class="px-4 py-3 text-center font-semibold text-[10px] uppercase">Estoque</th>
                        <th class="px-4 py-3 text-center font-semibold text-[10px] uppercase">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($kitTemplate->items as $item)
                    @php
                        $disponivel = $item->productTemplate->quantidadeDisponivel();
                        $ok = $disponivel >= $item->quantidade_minima;
                        $critColors = ['essencial' => 'bg-red-100 text-danger', 'sobressalente' => 'bg-yellow-100 text-warning', 'condicional' => 'bg-gray-100 text-gray-500'];
                    @endphp
                    <tr class="hover:bg-surface transition">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-text-primary">{{ $item->productTemplate->nome }}</p>
                            <p class="text-[10px] text-text-secondary">{{ $item->productTemplate->fabricante }} • {{ $item->productTemplate->codigo }}</p>
                            @if($item->observacoes)
                                <p class="text-[10px] text-text-secondary italic">{{ $item->observacoes }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-text-secondary">{{ $item->quantidade_minima }}</td>
                        <td class="px-4 py-3 text-center text-text-secondary">{{ $item->quantidade_recomendada }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $critColors[$item->criticidade] ?? '' }}">
                                {{ ucfirst($item->criticidade) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold {{ $ok ? 'text-success' : 'text-danger' }}">{{ $disponivel }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('kit-templates.remove-item', [$kitTemplate, $item]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Remover item?')" class="text-xs text-danger hover:underline">Remover</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-text-secondary italic">Nenhum item cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Adicionar item --}}
        <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm flex flex-col gap-4">
            <h3 class="font-bold text-text-primary">Adicionar Item</h3>
            <form action="{{ route('kit-templates.add-item', $kitTemplate) }}" method="POST" class="flex flex-col gap-3">
                @csrf
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Produto</label>
                    <select name="product_template_id" required class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                        <option value="">Selecione...</option>
                        @foreach($products->groupBy('fabricante') as $fab => $prods)
                        <optgroup label="{{ $fab }}">
                            @foreach($prods as $p)
                            <option value="{{ $p->id }}">{{ $p->nome }} ({{ $p->codigo }})</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs font-bold text-text-secondary uppercase">Qtd Mín.</label>
                        <input type="number" name="quantidade_minima" value="1" min="0" required class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-text-secondary uppercase">Qtd Rec.</label>
                        <input type="number" name="quantidade_recomendada" value="1" min="1" required class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Criticidade</label>
                    <select name="criticidade" required class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/30 outline-none">
                        <option value="essencial">Essencial</option>
                        <option value="sobressalente">Sobressalente</option>
                        <option value="condicional">Condicional</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-text-secondary uppercase">Observação</label>
                    <input type="text" name="observacoes" placeholder="Ex: Enviar tamanhos P/M/G" class="mt-1 w-full bg-white border border-border rounded-xl px-3 py-2 text-sm outline-none">
                </div>
                <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition">
                    Adicionar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
