<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the primary key and recreate without id column
        Schema::table('user_tenants', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
        });
        
        // Add composite primary key
        Schema::table('user_tenants', function (Blueprint $table) {
            $table->primary(['user_id', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tenants', function (Blueprint $table) {
            $table->dropPrimary(['user_id', 'tenant_id']);
            $table->uuid('id')->primary()->first();
        });
    }
};
