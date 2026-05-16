<?php

namespace App\Console\Commands;

use App\Modules\Audit\Models\AuditEvent;
use App\Modules\Material\Domain\Events\MaterialDiscarded;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Material\Domain\Models\MaterialLifecycleEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class CheckMaterialExpirations extends Command
{
    protected $signature   = 'materials:check-expirations';
    protected $description = 'Verifica materiais próximos ao vencimento e vencidos, gerando alertas no sistema.';

    private const TERMINAL_STATUSES = ['implantado_usado', 'descartado', 'devolvido_ao_fornecedor'];

    public function handle(): int
    {
        $cutoff = now()->addDays(30);

        $materials = Material::whereNotIn('status', self::TERMINAL_STATUSES)
            ->where('validade', '<=', $cutoff)
            ->get();

        $expired    = 0;
        $nearExpiry = 0;

        foreach ($materials as $material) {
            if ($material->isExpired()) {
                $expired++;
                $this->warn("VENCIDO: [{$material->lote}] {$material->nome} — venceu em {$material->validade->format('d/m/Y')}");

                Event::dispatch(new MaterialDiscarded(
                    materialId: $material->id,
                    actorId:    0,
                    actorRole:  'system',
                    occurredAt: now()->toISOString(),
                    metadata:   ['reason' => 'expiration_check', 'validade' => $material->validade->toDateString()],
                ));
            } else {
                $nearExpiry++;
                $daysLeft = now()->diffInDays($material->validade);
                $this->info("ALERTA: [{$material->lote}] {$material->nome} — vence em {$daysLeft} dia(s)");

                MaterialLifecycleEvent::create([
                    'event_type'  => 'expiry_warning',
                    'material_id' => $material->id,
                    'surgery_id'  => null,
                    'actor_id'    => null,
                    'actor_role'  => 'system',
                    'occurred_at' => now(),
                    'payload'     => [
                        'validade'     => $material->validade->toDateString(),
                        'days_left'    => $daysLeft,
                        'lote'         => $material->lote,
                    ],
                ]);
            }
        }

        // Audit summary
        AuditEvent::create([
            'actor_user_id' => null,
            'action'        => 'system.expiry_check',
            'entity_type'   => 'system',
            'entity_id'     => 0,
            'before'        => null,
            'after'         => null,
            'metadata'      => [
                'expired_count'     => $expired,
                'near_expiry_count' => $nearExpiry,
                'checked_at'        => now()->toISOString(),
            ],
        ]);

        $this->line("Verificação concluída: {$expired} vencido(s), {$nearExpiry} próximo(s) ao vencimento.");

        return self::SUCCESS;
    }
}
