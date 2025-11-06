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
        Schema::create('subscriber_segments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('criteria')->nullable(); // Filter criteria for dynamic segments
            $table->enum('type', ['static', 'dynamic'])->default('static');
            $table->integer('subscriber_count')->default(0);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'type']);
        });

        // Pivot table for static segment membership
        Schema::create('segment_subscriber', function (Blueprint $table) {
            $table->uuid('segment_id');
            $table->uuid('subscriber_id');
            $table->timestamps();

            $table->foreign('segment_id')->references('id')->on('subscriber_segments')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('newsletter_subscriptions')->onDelete('cascade');
            $table->primary(['segment_id', 'subscriber_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment_subscriber');
        Schema::dropIfExists('subscriber_segments');
    }
};
