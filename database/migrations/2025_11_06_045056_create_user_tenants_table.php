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
        Schema::create('user_tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('tenant_id');
            $table->enum('role', ['tenant_admin', 'editor', 'author', 'contributor'])->default('author');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->onDelete('cascade');

            // Unique constraint: user can only have one relationship per tenant
            $table->unique(['user_id', 'tenant_id']);
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('tenant_id');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tenants');
    }
};
