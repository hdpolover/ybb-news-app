<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('subject');
            $table->text('preview_text')->nullable();
            $table->longText('content');
            $table->enum('type', ['newsletter', 'digest', 'announcement', 'promotional'])->default('newsletter');
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled'])->default('draft');
            $table->json('recipient_criteria')->nullable();
            $table->unsignedInteger('estimated_recipients')->default(0);
            $table->unsignedInteger('actual_recipients')->default(0);
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->unsignedInteger('emails_sent')->default(0);
            $table->unsignedInteger('emails_delivered')->default(0);
            $table->unsignedInteger('emails_opened')->default(0);
            $table->unsignedInteger('emails_clicked')->default(0);
            $table->unsignedInteger('emails_bounced')->default(0);
            $table->unsignedInteger('emails_unsubscribed')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0.00);
            $table->decimal('click_rate', 5, 2)->default(0.00);
            $table->decimal('bounce_rate', 5, 2)->default(0.00);
            $table->string('template')->nullable();
            $table->json('settings')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
