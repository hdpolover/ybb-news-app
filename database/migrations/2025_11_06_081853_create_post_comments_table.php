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
        Schema::create('post_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->uuid('user_id'); // User who wrote the comment
            $table->uuid('parent_id')->nullable(); // For threaded comments
            $table->text('comment');
            $table->enum('type', ['internal', 'review', 'approval'])->default('internal');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('parent_id')
                ->references('id')->on('post_comments')
                ->onDelete('cascade');

            // Indexes
            $table->index('post_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('type');
            $table->index('is_resolved');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
