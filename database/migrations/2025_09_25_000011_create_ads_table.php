<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('banner');
            $table->string('placement');
            $table->json('content');
            $table->json('targeting')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(1);
            $table->integer('priority')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('max_impressions')->nullable();
            $table->integer('max_clicks')->nullable();
            $table->integer('current_impressions')->default(0);
            $table->integer('current_clicks')->default(0);
            $table->decimal('click_rate', 5, 2)->default(0.00);
            $table->string('status')->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
