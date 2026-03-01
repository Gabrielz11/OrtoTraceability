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
        Schema::create('surgery_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_item_id')->constrained()->cascadeOnDelete();
            $table->enum('acao', ['reservado', 'usado'])->default('reservado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surgery_material');
    }
};
