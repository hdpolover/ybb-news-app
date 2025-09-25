<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pt_program', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('post_id');
            $table->enum('program_type', ['scholarship', 'opportunity', 'internship']);
            $table->string('organizer_name');
            $table->string('location_text')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->boolean('is_rolling')->default(0);
            $table->enum('funding_type', ['fully_funded', 'partially_funded', 'unfunded'])->nullable();
            $table->decimal('stipend_amount', 18, 2)->nullable();
            $table->decimal('fee_amount', 18, 2)->nullable();
            $table->string('program_length_text')->nullable();
            $table->text('eligibility_text')->nullable();
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
        Schema::dropIfExists('pt_program');
    }
};
