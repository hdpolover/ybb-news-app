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
        Schema::table('pt_job', function (Blueprint $table) {
            // Drop foreign key first (before dropping index it depends on)
            $table->dropForeign('pt_job_tenant_id_foreign');
            
            // Drop unique constraint that includes tenant_id
            $table->dropUnique('pt_job_tenant_id_post_id_unique');
            
            // Drop the column
            $table->dropColumn('tenant_id');
            
            // Add new unique constraint without tenant_id
            $table->unique('post_id', 'pt_job_post_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pt_job', function (Blueprint $table) {
            // Add tenant_id back
            $table->char('tenant_id', 36)->after('id');
            
            // Restore foreign key
            $table->foreign('tenant_id', 'pt_job_tenant_id_foreign')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');
            
            // Drop simple unique and restore composite unique
            $table->dropUnique('pt_job_post_id_unique');
            $table->unique(['tenant_id', 'post_id'], 'pt_job_tenant_id_post_id_unique');
        });
    }
};
