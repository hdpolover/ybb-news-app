<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('from_url');
            $table->string('to_url');
            $table->enum('status_code', ['301', '302', '307', '308'])->default('301');
            $table->text('description')->nullable();
            $table->unsignedInteger('hits')->default(0);
            $table->dateTime('last_used_at')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_automatic')->default(0);
            $table->string('created_reason')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'from_url']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
