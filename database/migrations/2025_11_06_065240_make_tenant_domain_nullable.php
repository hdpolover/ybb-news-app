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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('domain')->nullable()->change();
            $table->dropUnique('tenants_domain_unique');
        });
        
        // Add unique index that ignores nulls
        Schema::table('tenants', function (Blueprint $table) {
            $table->unique('domain', 'tenants_domain_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('domain')->nullable(false)->change();
        });
    }
};
