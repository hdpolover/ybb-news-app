<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_color', 7)->default('#3b82f6');
            $table->string('secondary_color', 7)->default('#64748b');
            $table->string('accent_color', 7)->default('#10b981');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->string('google_adsense_id')->nullable();
            $table->string('google_tag_manager_id')->nullable();
            $table->string('email_from_name')->nullable();
            $table->string('email_from_address')->nullable();
            $table->boolean('gdpr_enabled')->default(0);
            $table->boolean('ccpa_enabled')->default(0);
            $table->text('privacy_policy_url')->nullable();
            $table->text('terms_of_service_url')->nullable();
            $table->json('enabled_features')->nullable();
            $table->json('settings')->nullable();
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
