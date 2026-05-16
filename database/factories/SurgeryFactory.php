<?php

namespace Database\Factories;

use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurgeryFactory extends Factory
{
    protected $model = Surgery::class;

    public function definition(): array
    {
        return [
            'data_hora'   => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'hospital'    => $this->faker->company() . ' Hospital',
            'medico'      => 'Dr. ' . $this->faker->name(),
            'paciente'    => 'Paciente ' . strtoupper($this->faker->bothify('?-###')),
            'status'      => 'agendada',
            'observacoes' => null,
        ];
    }

    public function realizada(): static
    {
        return $this->state([
            'status'   => 'realizada',
            'data_hora' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(['status' => 'cancelada']);
    }
}
