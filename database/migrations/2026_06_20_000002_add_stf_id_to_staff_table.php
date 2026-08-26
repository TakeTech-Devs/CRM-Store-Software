<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('stf_id')->nullable()->unique()->after('id');
        });

        // Backfill existing rows
        $store    = DB::table('store')->first();
        $storeKey = $store->id ?? 0;
        $staffs   = DB::table('staff')->orderBy('id')->get();
        $counter  = 0;

        foreach ($staffs as $staff) {
            $counter++;
            DB::table('staff')->where('id', $staff->id)->update([
                'stf_id'   => 'STF-' . $storeKey . '-' . str_pad($counter, 5, '0', STR_PAD_LEFT),
                'store_id' => $storeKey,
            ]);
        }

        Schema::table('staff', function (Blueprint $table) {
            $table->string('stf_id')->nullable(false)->change();
            $table->unique(['store_id', 'phone'], 'staff_store_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropUnique('staff_store_phone_unique');
            $table->dropColumn('stf_id');
        });
    }
};
