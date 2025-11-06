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
        Schema::table('media', function (Blueprint $table) {
            $table->string('folder')->nullable()->after('collection_name');
            $table->unsignedInteger('usage_count')->default(0)->after('folder');
            $table->index(['tenant_id', 'folder']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'folder']);
            $table->dropColumn(['folder', 'usage_count']);
        });
    }
};
