<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'remote_mysql';

    public function up(): void
    {
        $remote = Schema::connection('remote_mysql');

        // Every store shares this admin table but tracks its own migration state,
        // so only the first store to run this adds the columns (and only then are
        // existing rows backfilled to 'approved'); everyone after it finds the
        // column already there and skips, leaving real pending rows untouched.
        if (!$remote->hasTable('stock_writeoff') || $remote->hasColumn('stock_writeoff', 'status')) {
            return;
        }

        $remote->table('stock_writeoff', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('notes');
            $table->text('reject_reason')->nullable()->after('status');
            $table->timestamp('decided_at')->nullable()->after('reject_reason');
            $table->string('decided_by')->nullable()->after('decided_at');
        });

        $remote->getConnection()->statement("ALTER TABLE `stock_writeoff` ALTER COLUMN `status` SET DEFAULT 'pending'");
    }

    public function down(): void
    {
        $remote = Schema::connection('remote_mysql');

        if ($remote->hasColumn('stock_writeoff', 'status')) {
            $remote->table('stock_writeoff', function (Blueprint $table) {
                $table->dropColumn(['status', 'reject_reason', 'decided_at', 'decided_by']);
            });
        }
    }
};
