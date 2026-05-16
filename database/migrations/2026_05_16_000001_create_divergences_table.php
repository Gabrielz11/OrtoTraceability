<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divergences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('surgery_id')->nullable();
            $table->string('rule_name');
            $table->string('severity'); // critical, warning
            $table->text('message');
            $table->json('context')->nullable();
            $table->string('status')->default('open'); // open, acknowledged, resolved
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['material_id', 'status']);
            $table->index(['severity', 'status']);
            $table->foreign('material_id')->references('id')->on('material_items');
            $table->foreign('surgery_id')->references('id')->on('surgeries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divergences');
    }
};
