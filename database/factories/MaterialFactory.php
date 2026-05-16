<?php

namespace Database\Factories;

use App\Modules\Material\Domain\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        return [
            'nome'        => $this->faker->words(3, true),
            'lote'        => strtoupper($this->faker->bothify('L###-??')),
            'numero_serie' => $this->faker->optional()->bothify('SN-####'),
            'validade'    => $this->faker->dateTimeBetween('+1 month', '+3 years'),
            'fabricante'  => $this->faker->company(),
            'status'      => 'em_estoque',
            'observacoes' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(['validade' => now()->subDays(rand(1, 365))]);
    }

    public function nearExpiry(int $days = 15): static
    {
        return $this->state(['validade' => now()->addDays($days)]);
    }

    public function reserved(): static
    {
        return $this->state(['status' => 'reservado']);
    }

    public function used(): static
    {
        return $this->state(['status' => 'implantado_usado']);
    }
}
