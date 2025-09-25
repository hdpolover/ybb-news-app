<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pt_job', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('post_id');
            $table->string('company_name');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship']);
            $table->enum('workplace_type', ['onsite', 'hybrid', 'remote']);
            $table->string('title_override')->nullable();
            $table->string('location_city')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->decimal('min_salary', 18, 2)->nullable();
            $table->decimal('max_salary', 18, 2)->nullable();
            $table->string('salary_currency', 3)->nullable();
            $table->enum('salary_period', ['year', 'month', 'day', 'hour'])->nullable();
            $table->enum('experience_level', ['junior', 'mid', 'senior', 'lead'])->nullable();
            $table->longText('responsibilities')->nullable();
            $table->longText('requirements')->nullable();
            $table->json('benefits')->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->string('apply_url');
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'post_id']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pt_job');
    }
};
