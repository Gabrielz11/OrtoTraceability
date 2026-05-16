<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained('surgeries')->cascadeOnDelete();
            $table->foreignId('kit_template_id')->constrained('kit_templates');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', [
                'em_separacao', 'aguardando_conferencia', 'conferido',
                'despachado', 'recebido_hospital', 'em_esterilizacao',
                'pronto', 'utilizado', 'devolvido',
            ])->default('em_separacao');
            $table->timestamp('conferido_at')->nullable();
            $table->foreignId('conferido_por')->nullable()->constrained('users');
            $table->timestamp('despachado_at')->nullable();
            $table->timestamp('recebido_at')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['surgery_id', 'status']);
        });

        Schema::create('surgery_kit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_kit_id')->constrained('surgery_kits')->cascadeOnDelete();
            $table->foreignId('kit_template_item_id')->constrained('kit_template_items');
            $table->foreignId('stock_item_id')->nullable()->constrained('stock_items')->nullOnDelete();
            $table->enum('categoria', ['essencial', 'sobressalente', 'condicional'])->default('essencial');
            $table->enum('resultado', [
                'pendente', 'implantado_usado', 'consumido', 'devolvido_intacto', 'descartado',
            ])->default('pendente');
            $table->enum('motivo_descarte', [
                'contaminacao', 'queda', 'quebra', 'necessidade_tecnica', 'outro',
            ])->nullable();
            $table->text('observacao_resultado')->nullable();
            $table->boolean('dentro_autorizacao')->nullable();
            $table->timestamps();

            $table->index(['surgery_kit_id', 'resultado']);
            $table->index('stock_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_kit_items');
        Schema::dropIfExists('surgery_kits');
    }
};
