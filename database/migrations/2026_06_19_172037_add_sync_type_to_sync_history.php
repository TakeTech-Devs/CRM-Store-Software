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
        Schema::table('sync_history', function (Blueprint $table) {
            $table->string('sync_type')->nullable()->after('sync_date'); // 'Sync In' or 'Sync Out'
        });
    }

    public function down(): void
    {
        Schema::table('sync_history', function (Blueprint $table) {
            $table->dropColumn('sync_type');
        });
    }
};
