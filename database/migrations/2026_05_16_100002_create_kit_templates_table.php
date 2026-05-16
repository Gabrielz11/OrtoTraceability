<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kit_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('fabricante');
            $table->string('procedimento');
            $table->enum('tipo_kit', ['implante', 'instrumental', 'consumivel']);
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['fabricante', 'procedimento']);
        });

        Schema::create('kit_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_template_id')->constrained('kit_templates')->cascadeOnDelete();
            $table->foreignId('product_template_id')->constrained('product_templates');
            $table->integer('quantidade_minima')->default(1);
            $table->integer('quantidade_recomendada')->default(1);
            $table->enum('criticidade', ['essencial', 'sobressalente', 'condicional'])->default('essencial');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_template_items');
        Schema::dropIfExists('kit_templates');
    }
};
