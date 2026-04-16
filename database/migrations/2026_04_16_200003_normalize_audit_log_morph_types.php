<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize entity_type from fully-qualified class names to short morph map aliases.
        // This aligns existing data with the new Relation::morphMap() configuration.
        DB::table('audit_logs')
            ->where('entity_type', 'App\\Models\\MaterialItem')
            ->update(['entity_type' => 'material']);

        DB::table('audit_logs')
            ->where('entity_type', 'App\\Models\\Surgery')
            ->update(['entity_type' => 'surgery']);
    }

    public function down(): void
    {
        DB::table('audit_logs')
            ->where('entity_type', 'material')
            ->update(['entity_type' => 'App\\Models\\MaterialItem']);

        DB::table('audit_logs')
            ->where('entity_type', 'surgery')
            ->update(['entity_type' => 'App\\Models\\Surgery']);
    }
};
