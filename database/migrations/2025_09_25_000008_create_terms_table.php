<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['category', 'tag', 'location', 'skill', 'industry']);
            $table->uuid('parent_id')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('icon')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->unsignedInteger('post_count')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug', 'type']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('terms')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
