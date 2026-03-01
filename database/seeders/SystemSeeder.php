<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\MaterialItem;
use App\Models\Surgery;
use Illuminate\Support\Facades\Hash;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(['email' => 'admin@hospital.com'], [
            'name' => 'Admin Hospital',
            'password' => Hash::make('password'),
        ]);

        // Materials
        $materials = [
            ['nome' => 'Placa de Titânio 3.5mm', 'lote' => 'L001-24', 'validade' => now()->addMonths(6), 'fabricante' => 'Medtronic', 'status' => 'em_estoque'],
            ['nome' => 'Parafuso Cortical 2.0mm', 'lote' => 'L002-24', 'validade' => now()->addDays(5), 'fabricante' => 'Johnson & Johnson', 'status' => 'em_estoque'],
            ['nome' => 'Haste Intramedular', 'lote' => 'LXXX-OLD', 'validade' => now()->subMonths(1), 'fabricante' => 'Zimmer Biomet', 'status' => 'em_estoque'],
            ['nome' => 'Grampo Cirúrgico', 'lote' => 'B99-C1', 'validade' => now()->addMonths(12), 'fabricante' => 'Stryker', 'status' => 'em_estoque'],
            ['nome' => 'Fio de Sutura Aço', 'lote' => 'R-RESERVE-1', 'validade' => now()->addMonths(3), 'fabricante' => 'Medtronic', 'status' => 'reservado'],
        ];

        foreach ($materials as $m) {
            MaterialItem::create($m);
        }

        // Surgeries
        $surgeries = [
            [
                'data_hora' => now()->addDays(2),
                'hospital' => 'Hospital Santa Luzia',
                'medico' => 'Dr. Carlos Mendes',
                'paciente' => 'Maria Oliveira',
                'status' => 'agendada'
            ],
            [
                'data_hora' => now()->addDays(5),
                'hospital' => 'Hospital Albert Einstein',
                'medico' => 'Dra. Ana Paula',
                'paciente' => 'João Souza',
                'status' => 'agendada'
            ],
        ];

        foreach ($surgeries as $s) {
            Surgery::create($s);
        }
    }
}
