<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'remote_mysql';

    public function up(): void
    {
        if (Schema::connection('remote_mysql')->hasTable('doctor')
            && !Schema::connection('remote_mysql')->hasColumn('doctor', 'store_id')) {
            Schema::connection('remote_mysql')->table('doctor', function (Blueprint $table) {
                $table->unsignedBigInteger('store_id')->nullable()->after('id');
            });
        }

        // Existing admin doctor rows are left with store_id = NULL (their origin is
        // unknown/global) — only newly store-pushed doctors populate it going forward.
        if (Schema::connection('remote_mysql')->hasTable('doctor')
            && !$this->hasUniqueIndex('doctor', 'doctor_store_phone_unique')) {
            Schema::connection('remote_mysql')->table('doctor', function (Blueprint $table) {
                $table->unique(['store_id', 'phone'], 'doctor_store_phone_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('remote_mysql')->hasColumn('doctor', 'store_id')) {
            Schema::connection('remote_mysql')->table('doctor', function (Blueprint $table) {
                $table->dropUnique('doctor_store_phone_unique');
                $table->dropColumn('store_id');
            });
        }
    }

    private function hasUniqueIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::connection('remote_mysql')->getConnection()
            ->select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
