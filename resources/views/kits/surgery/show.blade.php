@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8" x-data="{ tab: 'essenciais' }">
    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('surgeries.show', $surgeryKit->surgery) }}" class="w-10 h-10 rounded-full border border-border flex items-center justify-center text-text-secondary hover:bg-surface transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-text-primary">{{ $surgeryKit->kitTemplate->nome }}</h1>
                <p class="text-text-secondary text-sm">{{ $surgeryKit->surgery->paciente }} — {{ $surgeryKit->surgery->data_hora->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <div class="flex flex-col items-end gap-2">
            @php
                $statusColors = [
                    'em_separacao' => 'bg-yellow-100 text-warning',
                    'aguardando_conferencia' => 'bg-orange-100 text-orange-700',
                    'conferido' => 'bg-blue-100 text-primary',
                    'despachado' => 'bg-purple-100 text-purple-700',
                    'recebido_hospital' => 'bg-green-100 text-success',
                    'em_esterilizacao' => 'bg-orange-100 text-orange-700',
                    'pronto' => 'bg-green-200 text-success',
                    'utilizado' => 'bg-gray-100 text-gray-600',
                    'devolvido' => 'bg-gray-100 text-gray-500',
                ];
            @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-bold {{ $statusColors[$surgeryKit->status] ?? 'bg-surface text-text-secondary' }}">
                {{ $surgeryKit->statusLabel() }}
            </span>
            <div class="flex gap-2">
                @if($surgeryKit->status === 'em_separacao')
                <form action="{{ route('surgery-kits.conferir', $surgeryKit) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-bold bg-primary text-white rounded-xl hover:bg-primary/90 transition">
                        Conferir Kit
                    </button>
                </form>
                @endif
                @if($surgeryKit->status === 'conferido')
                <form action="{{ route('surgery-kits.despachar', $surgeryKit) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-bold bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition">
                        Despachar
                    </button>
                </form>
                @endif
                @if($surgeryKit->status === 'despachado')
                <form action="{{ route('surgery-kits.receber', $surgeryKit) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-bold bg-success text-white rounded-xl hover:bg-green-700 transition">
                        Confirmar Recebimento
                    </button>
                </form>
                @endif
                @if(in_array($surgeryKit->status, ['recebido_hospital', 'pronto', 'utilizado']))
                <a href="{{ route('surgery-kits.resultado', $surgeryKit) }}" class="px-4 py-2 text-sm font-bold bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition">
                    Registrar Resultado
                </a>
                @endif
                @if(in_array($surgeryKit->status, ['conferido', 'despachado', 'recebido_hospital']))
                <form action="{{ route('surgery-kits.devolver', $surgeryKit) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Devolver todos os itens ao estoque?')" class="px-4 py-2 text-sm font-bold bg-white border border-border text-text-secondary rounded-xl hover:bg-red-50 hover:text-danger transition">
                        Devolver Kit
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Autorização --}}
    @if($surgeryKit->surgery->authorization)
    @php $auth = $surgeryKit->surgery->authorization; @endphp
    <div class="p-4 rounded-2xl border flex items-center gap-4 {{ $auth->isRecebida() ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
        <svg class="w-5 h-5 {{ $auth->isRecebida() ? 'text-success' : 'text-warning' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        <div class="flex-1">
            <p class="font-bold text-sm">{{ $auth->plano_saude }} — {{ strtoupper($auth->status) }}</p>
            @if($auth->codigo_autorizacao)
                <p class="text-xs text-text-secondary">Autorização: {{ $auth->codigo_autorizacao }}</p>
            @endif
        </div>
    </div>
    @else
    <div class="p-4 rounded-2xl border bg-orange-50 border-orange-200 flex items-center gap-3">
        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <p class="text-sm text-orange-700 font-medium">Sem autorização de plano de saúde cadastrada. <a href="{{ route('authorizations.create', $surgeryKit->surgery) }}" class="underline">Cadastrar</a></p>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b border-border">
        <button @click="tab = 'essenciais'" :class="tab === 'essenciais' ? 'border-primary text-primary' : 'border-transparent text-text-secondary'" class="px-6 py-3 font-semibold border-b-2 transition">
            Essenciais ({{ $surgeryKit->essenciais->count() }})
        </button>
        <button @click="tab = 'sobressalentes'" :class="tab === 'sobressalentes' ? 'border-primary text-primary' : 'border-transparent text-text-secondary'" class="px-6 py-3 font-semibold border-b-2 transition">
            Sobressalentes ({{ $surgeryKit->sobressalentes->count() }})
        </button>
    </div>

    @foreach([['essenciais', 'essenciais'], ['sobressalentes', 'sobressalentes']] as [$key, $label])
    <div x-show="tab === '{{ $key }}'">
        <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-surface border-b border-border text-text-secondary">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-[10px] uppercase">Produto Esperado</th>
                        <th class="px-5 py-3 text-left font-semibold text-[10px] uppercase">Item Vinculado</th>
                        <th class="px-5 py-3 text-center font-semibold text-[10px] uppercase">Status</th>
                        <th class="px-5 py-3 text-center font-semibold text-[10px] uppercase">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($surgeryKit->{$key} as $slot)
                    <tr class="hover:bg-surface transition">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-text-primary">{{ $slot->kitTemplateItem->productTemplate->nome }}</p>
                            <p class="text-[10px] text-text-secondary">Qtd rec.: {{ $slot->kitTemplateItem->quantidade_recomendada }}</p>
                        </td>
                        <td class="px-5 py-3">
                            @if($slot->stockItem)
                                <p class="font-medium text-text-primary">Lote: {{ $slot->stockItem->lote ?? '—' }}</p>
                                <p class="text-[10px] text-text-secondary">
                                    SN: {{ $slot->stockItem->numero_serie ?? '—' }}
                                    @if($slot->stockItem->tamanho) • Tam: {{ $slot->stockItem->tamanho }} @endif
                                    @if($slot->stockItem->validade)
                                        • Val: <span class="{{ $slot->stockItem->isExpired() ? 'text-danger font-bold' : ($slot->stockItem->isNearExpiry() ? 'text-warning' : '') }}">{{ $slot->stockItem->validade->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            @else
                                <span class="text-text-secondary italic text-xs">Nenhum item vinculado</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if(!$slot->stockItem)
                                <span class="text-danger">✗</span>
                            @elseif($slot->stockItem->isExpired())
                                <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-danger">Vencido</span>
                            @elseif($slot->stockItem->isNearExpiry())
                                <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-warning">Vencendo</span>
                            @else
                                <span class="text-success">✓</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($surgeryKit->status === 'em_separacao')
                                @if(!$slot->stockItem)
                                <form action="{{ route('surgery-kits.vincular-item', [$surgeryKit, $slot]) }}" method="POST" class="flex gap-2 justify-center">
                                    @csrf
                                    <select name="stock_item_id" required class="border border-border rounded-lg px-2 py-1 text-xs bg-white outline-none">
                                        <option value="">Selecionar...</option>
                                        @foreach($availableStock->filter(fn($s) => $s->product_template_id === $slot->kitTemplateItem->product_template_id) as $si)
                                        <option value="{{ $si->id }}">
                                            Lote: {{ $si->lote ?? '—' }} {{ $si->tamanho ? "| Tam: {$si->tamanho}" : '' }} | Val: {{ $si->validade?->format('d/m/Y') ?? '—' }}
                                        </option>
                                        @endforeach
                                        @if($availableStock->filter(fn($s) => $s->product_template_id === $slot->kitTemplateItem->product_template_id)->isEmpty())
                                        <option disabled>— sem estoque disponível —</option>
                                        @endif
                                    </select>
                                    <button type="submit" class="px-3 py-1 text-xs bg-primary text-white rounded-lg font-bold">Vincular</button>
                                </form>
                                @else
                                <form action="{{ route('surgery-kits.desvincular-item', [$surgeryKit, $slot]) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-danger hover:underline">Remover</button>
                                </form>
                                @endif
                            @else
                                <span class="text-xs text-text-secondary">{{ ucfirst($slot->resultado) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection
