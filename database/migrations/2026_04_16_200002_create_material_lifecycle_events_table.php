<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_lifecycle_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('surgery_id')->nullable();
            $table->unsignedBigInteger('actor_id');
            $table->string('actor_role');
            $table->timestamp('occurred_at');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['material_id', 'occurred_at']);
            $table->index('event_type');
            $table->index('surgery_id');

            $table->foreign('material_id')->references('id')->on('material_items')->cascadeOnDelete();
            $table->foreign('surgery_id')->references('id')->on('surgeries')->nullOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_lifecycle_events');
    }
};
