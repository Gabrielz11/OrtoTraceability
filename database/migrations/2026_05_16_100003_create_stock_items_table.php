<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_template_id')->constrained('product_templates');
            $table->string('lote')->nullable();
            $table->string('numero_serie')->nullable()->unique();
            $table->date('validade')->nullable();
            $table->string('tamanho')->nullable();
            $table->string('referencia_fabricante')->nullable();
            $table->integer('quantidade')->default(1);
            $table->enum('status', [
                'em_estoque', 'reservado', 'despachado',
                'em_esterilizacao', 'pronto_para_cirurgia',
                'implantado_usado', 'consumido', 'descartado', 'devolvido',
            ])->default('em_estoque');
            $table->enum('motivo_descarte', [
                'contaminacao', 'queda', 'quebra', 'uso_incorreto', 'vencimento', 'outro',
            ])->nullable();
            $table->text('observacao_descarte')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_template_id', 'status']);
            $table->index(['lote', 'numero_serie']);
            $table->index('validade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
