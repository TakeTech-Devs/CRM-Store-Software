<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_writeoff') || Schema::hasColumn('stock_writeoff', 'status')) {
            return;
        }

        // Added with default 'approved' so every write-off that already exists
        // (created before the approval workflow, already deducted from stock)
        // becomes Approved; the default is then flipped to 'pending' so only
        // genuinely new write-offs wait for admin review.
        Schema::table('stock_writeoff', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('notes');
            $table->text('reject_reason')->nullable()->after('status');
            $table->timestamp('decided_at')->nullable()->after('reject_reason');
            $table->timestamp('stock_restored_at')->nullable()->after('decided_at');
        });

        DB::statement("ALTER TABLE `stock_writeoff` ALTER COLUMN `status` SET DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_writeoff', 'status')) {
            Schema::table('stock_writeoff', function (Blueprint $table) {
                $table->dropColumn(['status', 'reject_reason', 'decided_at', 'stock_restored_at']);
            });
        }
    }
};
