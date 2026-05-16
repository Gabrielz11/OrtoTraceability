<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_templates', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('codigo_anvisa')->nullable();
            $table->string('nome');
            $table->string('fabricante');
            $table->enum('tipo', ['implante_esteril', 'instrumental', 'consumivel']);
            $table->enum('categoria', [
                'protese_quadril', 'protese_joelho', 'protese_ombro',
                'coluna', 'trauma', 'instrumental_geral', 'consumivel_geral',
            ]);
            $table->string('unidade_medida')->default('unidade');
            $table->boolean('requer_numero_serie')->default(false);
            $table->boolean('requer_lote')->default(true);
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['fabricante', 'tipo']);
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_templates');
    }
};
