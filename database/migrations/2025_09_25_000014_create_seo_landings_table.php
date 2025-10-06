<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seo_landings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('title');
            $table->string('slug');
            $table->text('meta_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('canonical_url')->nullable();
            $table->text('content')->nullable();
            $table->json('schema_markup')->nullable();
            $table->string('target_keyword')->nullable();
            $table->json('target_filters')->nullable();
            $table->enum('content_type', ['programs', 'jobs', 'mixed'])->default('mixed');
            $table->integer('items_per_page')->default(20);
            $table->unsignedInteger('views')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0.00);
            $table->boolean('is_active')->default(1);
            $table->enum('index_status', ['index', 'noindex'])->default('index');
            $table->enum('follow_status', ['follow', 'nofollow'])->default('follow');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('seo_landings');
    }
};
