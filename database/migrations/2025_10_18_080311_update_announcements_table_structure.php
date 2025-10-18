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
        Schema::table('announcements', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('announcements', 'content')) {
                $table->text('content')->after('title');
            }
            if (!Schema::hasColumn('announcements', 'image_url')) {
                $table->string('image_url')->nullable()->after('content');
            }
            if (!Schema::hasColumn('announcements', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image_url');
            }
            if (!Schema::hasColumn('announcements', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('announcements', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('published_at');
            }
            if (!Schema::hasColumn('announcements', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('expires_at');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['content', 'image_url', 'is_active', 'published_at', 'expires_at', 'created_by']);
        });
    }
};
