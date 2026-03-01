@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">Trilha de Auditoria</h1>
            <p class="text-text-secondary mt-1">Registros imutáveis de todas as operações críticas do sistema.</p>
        </div>
    </div>

    <!-- Audit Table -->
    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface border-b border-border text-text-secondary">
                <tr>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Data / Hora</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Ação</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Entidade</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Resumo da Alteração</th>
                    <th class="px-6 py-4 font-semibold text-[10px] uppercase tracking-wider">Usuário</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($logs as $log)
                <tr class="hover:bg-surface-light transition">
                    <td class="px-6 py-4 text-text-secondary whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border 
                            @if(in_array($log->action, ['create', 'link'])) bg-green-50 text-success border-success/20 
                            @elseif($log->action == 'delete' || $log->action == 'unlink') bg-red-50 text-danger border-danger/20 
                            @else bg-blue-50 text-primary border-primary/20 @endif">
                            {{ strtoupper($log->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-text-primary font-medium">
                        {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-xs overflow-hidden text-ellipsis whitespace-nowrap text-xs text-text-secondary font-mono" title="{{ json_encode($log->after ?? $log->metadata) }}">
                            {{ json_encode($log->after ?? $log->metadata) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-text-secondary">
                        {{ $log->user?->name ?? 'Sistema/Admin' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-text-secondary italic">Nenhum registro de auditoria encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-border bg-surface">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
