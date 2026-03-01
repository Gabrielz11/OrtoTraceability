<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_items', function (Blueprint $table) {
            $table->id();
            $table->string('lote');
            $table->string('numero_serie')->nullable()->unique();
            $table->date('validade');
            $table->string('fabricante');
            $table->enum('status', [
                'em_estoque',
                'reservado',
                'implantado_usado',
                'descartado',
                'devolvido_ao_fornecedor'
            ])->default('em_estoque');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['lote', 'numero_serie', 'validade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_items');
    }
};
