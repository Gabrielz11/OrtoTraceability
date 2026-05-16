<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_templates', function (Blueprint $table) {
            $table->string('udi_di')->nullable()->after('fabricante');
            $table->enum('udi_issuing_agency', ['GS1', 'HIBCC', 'ICCBBA'])->nullable()->after('udi_di');
            $table->boolean('udi_required')->default(false)->after('udi_issuing_agency');

            $table->index('ativo');
            $table->index('udi_di');
        });
    }

    public function down(): void
    {
        Schema::table('product_templates', function (Blueprint $table) {
            $table->dropIndex(['ativo']);
            $table->dropIndex(['udi_di']);
            $table->dropColumn(['udi_di', 'udi_issuing_agency', 'udi_required']);
        });
    }
};
