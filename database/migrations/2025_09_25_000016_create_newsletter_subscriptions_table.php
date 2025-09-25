<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('email');
            $table->string('name')->nullable();
            $table->json('preferences')->nullable();
            $table->enum('status', ['active', 'unsubscribed', 'bounced', 'pending'])->default('pending');
            $table->string('frequency')->default('weekly');
            $table->string('verification_token')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->string('unsubscribe_token');
            $table->json('tags')->nullable();
            $table->string('source')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->dateTime('last_sent_at')->nullable();
            $table->unsignedInteger('emails_sent')->default(0);
            $table->unsignedInteger('emails_opened')->default(0);
            $table->unsignedInteger('links_clicked')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'email']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriptions');
    }
};
