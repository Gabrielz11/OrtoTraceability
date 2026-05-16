<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Kit\Domain\Models\KitTemplate;
use App\Modules\Kit\Domain\Models\KitTemplateItem;
use App\Modules\Material\Domain\Models\Material;
use App\Modules\Material\Domain\Models\MaterialLifecycleEvent;
use App\Modules\Stock\Domain\Models\ProductTemplate;
use App\Modules\Stock\Domain\Models\StockItem;
use App\Modules\Surgery\Domain\Models\Surgery;
use App\Modules\Validation\Domain\Models\Divergence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuários ────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@demo.com'], [
            'name'     => 'Admin Demo',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $instrumentador = User::firstOrCreate(['email' => 'instrumentador@demo.com'], [
            'name'     => 'Lucas Instrumentador',
            'password' => Hash::make('password'),
            'role'     => 'instrumentator',
        ]);

        $auditor = User::firstOrCreate(['email' => 'auditor@demo.com'], [
            'name'     => 'Ana Auditora',
            'password' => Hash::make('password'),
            'role'     => 'auditor',
        ]);

        // ── Materiais ───────────────────────────────────────────
        $materialsData = [
            // Em estoque — normais (0–7)
            ['nome' => 'Placa de Titânio 3.5mm',      'lote' => 'L001-25', 'numero_serie' => 'SN-1001', 'validade' => now()->addMonths(18), 'fabricante' => 'Medtronic',         'status' => 'em_estoque'],
            ['nome' => 'Parafuso Cortical 2.0mm',      'lote' => 'L002-25', 'numero_serie' => 'SN-1002', 'validade' => now()->addMonths(24), 'fabricante' => 'Johnson & Johnson', 'status' => 'em_estoque'],
            ['nome' => 'Haste Femoral Longa',           'lote' => 'L003-25', 'numero_serie' => 'SN-1003', 'validade' => now()->addMonths(36), 'fabricante' => 'Zimmer Biomet',     'status' => 'em_estoque'],
            ['nome' => 'Grampo Cirúrgico 40mm',         'lote' => 'L004-25', 'numero_serie' => null,       'validade' => now()->addMonths(12), 'fabricante' => 'Stryker',           'status' => 'em_estoque'],
            ['nome' => 'Fio de Sutura Aço 2-0',        'lote' => 'L005-25', 'numero_serie' => null,       'validade' => now()->addMonths(30), 'fabricante' => 'Medtronic',         'status' => 'em_estoque'],
            ['nome' => 'Prótese Joelho Total',          'lote' => 'L006-25', 'numero_serie' => 'SN-1006', 'validade' => now()->addMonths(60), 'fabricante' => 'DePuy Synthes',     'status' => 'em_estoque'],
            ['nome' => 'Parafuso Pedicular 5.5x45',    'lote' => 'L007-25', 'numero_serie' => 'SN-1007', 'validade' => now()->addMonths(48), 'fabricante' => 'Medtronic',         'status' => 'em_estoque'],
            ['nome' => 'Cage Intersomático TLIF',       'lote' => 'L008-25', 'numero_serie' => 'SN-1008', 'validade' => now()->addMonths(60), 'fabricante' => 'Globus Medical',    'status' => 'em_estoque'],
            // Em estoque — próximos ao vencimento (8–9)
            ['nome' => 'Placa Bloqueada 4.5mm',        'lote' => 'L009-24', 'numero_serie' => 'SN-1009', 'validade' => now()->addDays(20),   'fabricante' => 'Synthes',           'status' => 'em_estoque'],
            ['nome' => 'Agulha de Artroscopia 2.5mm',  'lote' => 'L010-24', 'numero_serie' => null,       'validade' => now()->addDays(10),   'fabricante' => 'Arthrex',           'status' => 'em_estoque'],
            // Em estoque — vencido (10)
            ['nome' => 'Haste Intramedular Antiga',    'lote' => 'LXXX-OLD', 'numero_serie' => 'SN-0001', 'validade' => now()->subMonths(2),  'fabricante' => 'Zimmer Biomet',     'status' => 'em_estoque'],
            // Reservados (11–18)
            ['nome' => 'Prótese de Quadril Cimentada', 'lote' => 'L011-25', 'numero_serie' => 'SN-1011', 'validade' => now()->addMonths(24), 'fabricante' => 'Smith+Nephew',      'status' => 'reservado'],
            ['nome' => 'Parafuso Canulado 7.3mm',      'lote' => 'L012-25', 'numero_serie' => 'SN-1012', 'validade' => now()->addMonths(18), 'fabricante' => 'Stryker',           'status' => 'reservado'],
            ['nome' => 'Placa Bloqueada Distal Rádio', 'lote' => 'L013-25', 'numero_serie' => 'SN-1013', 'validade' => now()->addMonths(30), 'fabricante' => 'DePuy Synthes',     'status' => 'reservado'],
            ['nome' => 'Haste Tibial Sólida',          'lote' => 'L014-25', 'numero_serie' => 'SN-1014', 'validade' => now()->addMonths(36), 'fabricante' => 'Medtronic',         'status' => 'reservado'],
            ['nome' => 'Âncora de Sutura 5mm',         'lote' => 'L015-25', 'numero_serie' => null,       'validade' => now()->addMonths(12), 'fabricante' => 'Arthrex',           'status' => 'reservado'],
            ['nome' => 'Fixador Externo Modular',      'lote' => 'L016-25', 'numero_serie' => 'SN-1016', 'validade' => now()->addMonths(48), 'fabricante' => 'Synthes',           'status' => 'reservado'],
            ['nome' => 'Prótese Total de Ombro',       'lote' => 'L017-25', 'numero_serie' => 'SN-1017', 'validade' => now()->addMonths(60), 'fabricante' => 'Zimmer Biomet',     'status' => 'reservado'],
            ['nome' => 'Parafuso de Interferência',    'lote' => 'L018-25', 'numero_serie' => null,       'validade' => now()->addMonths(24), 'fabricante' => 'Arthrex',           'status' => 'reservado'],
            // Implantados/Usados (18–25)
            ['nome' => 'Placa Osteossíntese 2.7mm',   'lote' => 'L019-24', 'numero_serie' => 'SN-0019', 'validade' => now()->addMonths(6),  'fabricante' => 'Synthes',           'status' => 'implantado_usado'],
            ['nome' => 'Parafuso Esponjoso 4.5mm',    'lote' => 'L020-24', 'numero_serie' => 'SN-0020', 'validade' => now()->addMonths(8),  'fabricante' => 'Medtronic',         'status' => 'implantado_usado'],
            ['nome' => 'Haste Cefalomedular',          'lote' => 'L021-24', 'numero_serie' => 'SN-0021', 'validade' => now()->addMonths(4),  'fabricante' => 'Stryker',           'status' => 'implantado_usado'],
            ['nome' => 'Prótese Parcial Quadril',     'lote' => 'L022-24', 'numero_serie' => 'SN-0022', 'validade' => now()->addMonths(12), 'fabricante' => 'DePuy Synthes',     'status' => 'implantado_usado'],
            ['nome' => 'Fixação Vertebral L4-L5',     'lote' => 'L023-24', 'numero_serie' => 'SN-0023', 'validade' => now()->addMonths(24), 'fabricante' => 'Globus Medical',    'status' => 'implantado_usado'],
            ['nome' => 'Placa de Reconstrução',       'lote' => 'L024-24', 'numero_serie' => 'SN-0024', 'validade' => now()->addMonths(9),  'fabricante' => 'Synthes',           'status' => 'implantado_usado'],
            ['nome' => 'Prótese Total de Joelho Rev.','lote' => 'L025-24', 'numero_serie' => 'SN-0025', 'validade' => now()->addMonths(18), 'fabricante' => 'Smith+Nephew',      'status' => 'implantado_usado'],
            ['nome' => 'Parafuso Poliaxial 6.5mm',   'lote' => 'L026-24', 'numero_serie' => 'SN-0026', 'validade' => now()->addMonths(6),  'fabricante' => 'Medtronic',         'status' => 'implantado_usado'],
            // Descartados (26–27)
            ['nome' => 'Espaçador Articular Temp.',   'lote' => 'LDESC-1', 'numero_serie' => null,       'validade' => now()->subMonths(1),  'fabricante' => 'Biomet',            'status' => 'descartado'],
            ['nome' => 'Fio Kirchner 2.0mm',          'lote' => 'LDESC-2', 'numero_serie' => null,       'validade' => now()->subDays(5),    'fabricante' => 'Synthes',           'status' => 'descartado'],
            // Devolvidos (28–29)
            ['nome' => 'Parafuso Canulado Defeito',   'lote' => 'LDEV-1',  'numero_serie' => 'SN-D001',  'validade' => now()->addMonths(6),  'fabricante' => 'Stryker',           'status' => 'devolvido_ao_fornecedor'],
            ['nome' => 'Placa Titânio Lote Recall',   'lote' => 'LDEV-2',  'numero_serie' => 'SN-D002',  'validade' => now()->addMonths(12), 'fabricante' => 'Medtronic',         'status' => 'devolvido_ao_fornecedor'],
        ];

        $materials = [];
        foreach ($materialsData as $m) {
            $materials[] = Material::create($m);
        }

        // ── Cirurgias ───────────────────────────────────────────
        $surgeries = [];

        $surgeries[] = Surgery::create([
            'data_hora'   => now()->addDays(3),
            'hospital'    => 'Hospital Santa Luzia',
            'medico'      => 'Dr. Carlos Mendes',
            'paciente'    => 'Paciente A-001',
            'status'      => 'agendada',
            'observacoes' => 'Artroplastia total de quadril esquerdo.',
        ]);

        $surgeries[] = Surgery::create([
            'data_hora'   => now()->addDays(7),
            'hospital'    => 'Hospital Albert Einstein',
            'medico'      => 'Dra. Fernanda Lopes',
            'paciente'    => 'Paciente B-002',
            'status'      => 'agendada',
            'observacoes' => 'Fixação de fratura femoral com haste intramedular.',
        ]);

        $surgeries[] = Surgery::create([
            'data_hora'   => now()->subDays(10),
            'hospital'    => 'Hospital das Clínicas',
            'medico'      => 'Dr. Roberto Farias',
            'paciente'    => 'Paciente C-003',
            'status'      => 'realizada',
            'observacoes' => 'Fixação vertebral L4-L5 com parafusos pediculares.',
        ]);

        $surgeries[] = Surgery::create([
            'data_hora'   => now()->subDays(5),
            'hospital'    => 'Hospital São Paulo',
            'medico'      => 'Dra. Camila Souza',
            'paciente'    => 'Paciente D-004',
            'status'      => 'realizada',
            'observacoes' => 'Artroscopia de joelho com âncora de sutura.',
        ]);

        $surgeries[] = Surgery::create([
            'data_hora'   => now()->subDays(2),
            'hospital'    => 'Hospital Santa Luzia',
            'medico'      => 'Dr. Paulo Lima',
            'paciente'    => 'Paciente E-005',
            'status'      => 'cancelada',
            'observacoes' => 'Cancelada por condição clínica do paciente.',
        ]);

        // Vínculos cirurgia-material
        $surgeries[0]->materials()->attach($materials[11]->id, ['acao' => 'reservado']);
        $surgeries[0]->materials()->attach($materials[12]->id, ['acao' => 'reservado']);
        $surgeries[1]->materials()->attach($materials[13]->id, ['acao' => 'reservado']);
        $surgeries[1]->materials()->attach($materials[14]->id, ['acao' => 'reservado']);
        $surgeries[2]->materials()->attach($materials[18]->id, ['acao' => 'usado']);
        $surgeries[2]->materials()->attach($materials[22]->id, ['acao' => 'usado']);
        $surgeries[3]->materials()->attach($materials[19]->id, ['acao' => 'usado']);

        // ── Lifecycle Events ────────────────────────────────────
        $now = now();

        MaterialLifecycleEvent::create([
            'event_type'  => 'material.received',
            'material_id' => $materials[18]->id,
            'surgery_id'  => null,
            'actor_id'    => $instrumentador->id,
            'actor_role'  => 'instrumentator',
            'occurred_at' => $now->copy()->subDays(30),
            'payload'     => ['lote' => $materials[18]->lote, 'fabricante' => $materials[18]->fabricante],
        ]);

        MaterialLifecycleEvent::create([
            'event_type'  => 'material.allocated_to_surgery',
            'material_id' => $materials[18]->id,
            'surgery_id'  => $surgeries[2]->id,
            'actor_id'    => $instrumentador->id,
            'actor_role'  => 'instrumentator',
            'occurred_at' => $now->copy()->subDays(12),
            'payload'     => ['surgery_id' => $surgeries[2]->id],
        ]);

        MaterialLifecycleEvent::create([
            'event_type'  => 'material.used',
            'material_id' => $materials[18]->id,
            'surgery_id'  => $surgeries[2]->id,
            'actor_id'    => $instrumentador->id,
            'actor_role'  => 'instrumentator',
            'occurred_at' => $now->copy()->subDays(10),
            'payload'     => ['surgery_id' => $surgeries[2]->id, 'status_final' => 'implantado_usado'],
        ]);

        MaterialLifecycleEvent::create([
            'event_type'  => 'material.received',
            'material_id' => $materials[10]->id,
            'surgery_id'  => null,
            'actor_id'    => $admin->id,
            'actor_role'  => 'admin',
            'occurred_at' => $now->copy()->subMonths(8),
            'payload'     => ['lote' => $materials[10]->lote, 'validade' => $materials[10]->validade->toDateString()],
        ]);

        // ── Divergências de Exemplo ─────────────────────────────
        Divergence::create([
            'material_id' => $materials[10]->id,
            'surgery_id'  => null,
            'rule_name'   => 'MaterialNotExpiredRule',
            'severity'    => 'critical',
            'message'     => 'Material com validade expirada há ' . abs($materials[10]->validade->diffInDays()) . ' dias.',
            'context'     => ['validade' => $materials[10]->validade->toDateString(), 'status' => 'em_estoque'],
            'status'      => 'open',
            'occurred_at' => now()->subDays(3),
        ]);

        Divergence::create([
            'material_id' => $materials[8]->id,
            'surgery_id'  => null,
            'rule_name'   => 'MaterialNearExpiryWarningRule',
            'severity'    => 'warning',
            'message'     => 'Material expira em menos de 30 dias.',
            'context'     => ['validade' => $materials[8]->validade->toDateString()],
            'status'      => 'open',
            'occurred_at' => now()->subDays(1),
        ]);

        Divergence::create([
            'material_id'     => $materials[18]->id,
            'surgery_id'      => $surgeries[2]->id,
            'rule_name'       => 'MaterialAllocatedBeforeUseRule',
            'severity'        => 'warning',
            'message'         => 'Verificação de alocação prévia ao uso — resolvida manualmente.',
            'context'         => ['surgery_id' => $surgeries[2]->id],
            'status'          => 'resolved',
            'acknowledged_by' => $auditor->id,
            'acknowledged_at' => now()->subDays(9),
            'occurred_at'     => now()->subDays(10),
        ]);

        // ── Catálogo de Produtos ────────────────────────────────
        $productsData = [
            ['codigo' => 'MED-CUPULA-52',    'nome' => 'Cúpula Acetabular 52mm',      'fabricante' => 'Medacta', 'tipo' => 'implante_esteril', 'categoria' => 'protese_quadril', 'requer_numero_serie' => true],
            ['codigo' => 'MED-CUPULA-54',    'nome' => 'Cúpula Acetabular 54mm',      'fabricante' => 'Medacta', 'tipo' => 'implante_esteril', 'categoria' => 'protese_quadril', 'requer_numero_serie' => true],
            ['codigo' => 'MED-CABECA-CER-28','nome' => 'Cabeça Cerâmica 28mm',        'fabricante' => 'Medacta', 'tipo' => 'implante_esteril', 'categoria' => 'protese_quadril', 'requer_numero_serie' => true],
            ['codigo' => 'MED-CABECA-MET-28','nome' => 'Cabeça Metálica 28mm',        'fabricante' => 'Medacta', 'tipo' => 'implante_esteril', 'categoria' => 'protese_quadril', 'requer_numero_serie' => true],
            ['codigo' => 'MED-HASTE-12',     'nome' => 'Haste Femoral 12mm',          'fabricante' => 'Medacta', 'tipo' => 'implante_esteril', 'categoria' => 'protese_quadril', 'requer_numero_serie' => true],
            ['codigo' => 'MED-HASTE-14',     'nome' => 'Haste Femoral 14mm',          'fabricante' => 'Medacta', 'tipo' => 'implante_esteril', 'categoria' => 'protese_quadril', 'requer_numero_serie' => true],
            ['codigo' => 'MED-INSERT-PE',    'nome' => 'Insert de Polietileno 52mm',  'fabricante' => 'Medacta', 'tipo' => 'implante_esteril', 'categoria' => 'protese_quadril', 'requer_numero_serie' => false],
            ['codigo' => 'MED-CX-QUADRIL',   'nome' => 'Caixa Cirúrgica Quadril Medacta', 'fabricante' => 'Medacta', 'tipo' => 'instrumental', 'categoria' => 'protese_quadril', 'requer_numero_serie' => false, 'requer_lote' => false],
            ['codigo' => 'MED-PROVAS-QDL',   'nome' => 'Set de Provas Quadril',       'fabricante' => 'Medacta', 'tipo' => 'instrumental', 'categoria' => 'protese_quadril', 'requer_numero_serie' => false, 'requer_lote' => false],
            ['codigo' => 'CONS-CIMENTO-SX',  'nome' => 'Cimento Ósseo Simplex 40g',  'fabricante' => 'Stryker', 'tipo' => 'consumivel', 'categoria' => 'consumivel_geral', 'requer_lote' => true],
            ['codigo' => 'CONS-IOBAN-35',    'nome' => 'Ioban 35×45cm',              'fabricante' => '3M',      'tipo' => 'consumivel', 'categoria' => 'consumivel_geral', 'requer_lote' => false],
            ['codigo' => 'CONS-CIMENTO-ATB', 'nome' => 'Cimento c/ Antibiótico Palacos R+G', 'fabricante' => 'Heraeus', 'tipo' => 'consumivel', 'categoria' => 'consumivel_geral', 'requer_lote' => true],
        ];

        $catalogProducts = [];
        foreach ($productsData as $pd) {
            $catalogProducts[] = ProductTemplate::create(array_merge([
                'requer_numero_serie' => false,
                'requer_lote'         => true,
                'ativo'               => true,
                'codigo_anvisa'       => null,
                'unidade_medida'      => 'unidade',
                'observacoes'         => null,
            ], $pd));
        }

        // ── Template de Kit: Artroplastia Total de Quadril Medacta ──
        $kitImplante = KitTemplate::create([
            'nome'        => 'Kit Prótese Quadril Medacta — Implantes',
            'fabricante'  => 'Medacta',
            'procedimento' => 'Artroplastia Total de Quadril',
            'tipo_kit'    => 'implante',
            'descricao'   => 'Kit completo de implantes estéreis para ATQ Medacta.',
        ]);

        // Cúpula (essencial, 3 tamanhos recomendados)
        KitTemplateItem::create(['kit_template_id' => $kitImplante->id, 'product_template_id' => $catalogProducts[0]->id, 'quantidade_minima' => 1, 'quantidade_recomendada' => 2, 'criticidade' => 'essencial', 'observacoes' => 'Enviar tamanhos 52 e 54']);
        KitTemplateItem::create(['kit_template_id' => $kitImplante->id, 'product_template_id' => $catalogProducts[1]->id, 'quantidade_minima' => 0, 'quantidade_recomendada' => 1, 'criticidade' => 'sobressalente', 'observacoes' => 'Tamanho extra']);
        // Cabeça cerâmica (essencial)
        KitTemplateItem::create(['kit_template_id' => $kitImplante->id, 'product_template_id' => $catalogProducts[2]->id, 'quantidade_minima' => 1, 'quantidade_recomendada' => 2, 'criticidade' => 'essencial']);
        // Cabeça metálica (sobressalente)
        KitTemplateItem::create(['kit_template_id' => $kitImplante->id, 'product_template_id' => $catalogProducts[3]->id, 'quantidade_minima' => 0, 'quantidade_recomendada' => 1, 'criticidade' => 'sobressalente']);
        // Haste femoral (essencial)
        KitTemplateItem::create(['kit_template_id' => $kitImplante->id, 'product_template_id' => $catalogProducts[4]->id, 'quantidade_minima' => 1, 'quantidade_recomendada' => 2, 'criticidade' => 'essencial', 'observacoes' => 'Tamanhos 12 e 14']);
        // Insert (essencial)
        KitTemplateItem::create(['kit_template_id' => $kitImplante->id, 'product_template_id' => $catalogProducts[6]->id, 'quantidade_minima' => 1, 'quantidade_recomendada' => 1, 'criticidade' => 'essencial']);

        $kitInstrumental = KitTemplate::create([
            'nome'        => 'Kit Caixa Cirúrgica Quadril Medacta — Instrumental',
            'fabricante'  => 'Medacta',
            'procedimento' => 'Artroplastia Total de Quadril',
            'tipo_kit'    => 'instrumental',
        ]);

        KitTemplateItem::create(['kit_template_id' => $kitInstrumental->id, 'product_template_id' => $catalogProducts[7]->id, 'quantidade_minima' => 1, 'quantidade_recomendada' => 1, 'criticidade' => 'essencial']);
        KitTemplateItem::create(['kit_template_id' => $kitInstrumental->id, 'product_template_id' => $catalogProducts[8]->id, 'quantidade_minima' => 1, 'quantidade_recomendada' => 1, 'criticidade' => 'essencial']);

        // ── Stock Items (instâncias físicas dos produtos) ───────
        StockItem::create(['product_template_id' => $catalogProducts[0]->id, 'lote' => 'MED-CUP52-A', 'numero_serie' => 'SN-CUP-0001', 'validade' => now()->addMonths(24), 'tamanho' => '52mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[0]->id, 'lote' => 'MED-CUP52-B', 'numero_serie' => 'SN-CUP-0002', 'validade' => now()->addMonths(18), 'tamanho' => '52mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[1]->id, 'lote' => 'MED-CUP54-A', 'numero_serie' => 'SN-CUP-0003', 'validade' => now()->addMonths(24), 'tamanho' => '54mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[2]->id, 'lote' => 'MED-CAB-CER-A', 'numero_serie' => 'SN-CAB-0001', 'validade' => now()->addMonths(36), 'tamanho' => '28mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[2]->id, 'lote' => 'MED-CAB-CER-B', 'numero_serie' => 'SN-CAB-0002', 'validade' => now()->addMonths(30), 'tamanho' => '28mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[3]->id, 'lote' => 'MED-CAB-MET-A', 'numero_serie' => 'SN-CABM-001', 'validade' => now()->addMonths(36), 'tamanho' => '28mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[4]->id, 'lote' => 'MED-HASTE-A',   'numero_serie' => 'SN-HASTE-001', 'validade' => now()->addMonths(24), 'tamanho' => '12mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[5]->id, 'lote' => 'MED-HASTE-B',   'numero_serie' => 'SN-HASTE-002', 'validade' => now()->addMonths(20), 'tamanho' => '14mm', 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[6]->id, 'lote' => 'MED-INS-A',     'numero_serie' => null,            'validade' => now()->addMonths(24), 'tamanho' => '52mm', 'status' => 'em_estoque', 'quantidade' => 2]);
        StockItem::create(['product_template_id' => $catalogProducts[9]->id, 'lote' => 'CIMENTO-LOT-A', 'numero_serie' => null, 'validade' => now()->addDays(15), 'status' => 'em_estoque', 'quantidade' => 3]);
        StockItem::create(['product_template_id' => $catalogProducts[10]->id,'lote' => null,             'numero_serie' => null, 'validade' => null,                'status' => 'em_estoque', 'quantidade' => 10]);
        StockItem::create(['product_template_id' => $catalogProducts[11]->id,'lote' => 'PAL-LOT-A',     'numero_serie' => null, 'validade' => now()->addMonths(12), 'status' => 'em_estoque', 'quantidade' => 2]);
        // Caixa instrumental (sem validade, sem lote)
        StockItem::create(['product_template_id' => $catalogProducts[7]->id, 'lote' => null, 'numero_serie' => 'CX-QDL-001', 'validade' => null, 'status' => 'em_estoque', 'quantidade' => 1]);
        StockItem::create(['product_template_id' => $catalogProducts[8]->id, 'lote' => null, 'numero_serie' => 'PROVAS-001',  'validade' => null, 'status' => 'em_estoque', 'quantidade' => 1]);
    }
}
