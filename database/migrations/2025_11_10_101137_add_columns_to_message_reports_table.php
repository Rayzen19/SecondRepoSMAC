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
        Schema::table('message_reports', function (Blueprint $table) {
            $table->foreignId('message_id')->after('id')->constrained('messages')->onDelete('cascade');
            $table->foreignId('reported_by')->after('message_id')->constrained('users')->onDelete('cascade');
            $table->string('reason')->after('reported_by');
            $table->text('details')->nullable()->after('reason');
            $table->enum('status', ['pending', 'reviewed', 'dismissed', 'action_taken'])->default('pending')->after('details');
            $table->foreignId('reviewed_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable()->after('reviewed_by');
            $table->timestamp('reviewed_at')->nullable()->after('admin_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_reports', function (Blueprint $table) {
            $table->dropForeign(['message_id']);
            $table->dropForeign(['reported_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'message_id',
                'reported_by',
                'reason',
                'details',
                'status',
                'reviewed_by',
                'admin_notes',
                'reviewed_at'
            ]);
        });
    }
};
