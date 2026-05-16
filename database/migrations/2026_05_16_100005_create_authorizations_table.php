<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained('surgeries');
            $table->string('plano_saude');
            $table->string('codigo_autorizacao')->nullable();
            $table->date('data_autorizacao')->nullable();
            $table->date('validade_autorizacao')->nullable();
            $table->enum('status', [
                'nao_recebida', 'recebida', 'parcial', 'vencida',
            ])->default('nao_recebida');
            $table->text('observacoes')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['surgery_id', 'status']);
        });

        Schema::create('authorization_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('authorization_id')->constrained('authorizations')->cascadeOnDelete();
            $table->string('codigo_produto')->nullable();
            $table->string('descricao_produto');
            $table->integer('quantidade_autorizada');
            $table->decimal('valor_unitario', 10, 2)->nullable();
            $table->boolean('coberto')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorization_items');
        Schema::dropIfExists('authorizations');
    }
};
