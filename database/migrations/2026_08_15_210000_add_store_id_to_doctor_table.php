<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
        });

        // Single-store-per-install — tag existing rows with this install's store so
        // they become eligible for the new Sync Out push going forward.
        $storeId = DB::table('store')->value('id');
        if ($storeId) {
            DB::table('doctor')->update(['store_id' => $storeId]);
        }

        Schema::table('doctor', function (Blueprint $table) {
            $table->unique(['store_id', 'phone'], 'doctor_store_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('doctor', function (Blueprint $table) {
            $table->dropUnique('doctor_store_phone_unique');
            $table->dropColumn('store_id');
        });
    }
};
