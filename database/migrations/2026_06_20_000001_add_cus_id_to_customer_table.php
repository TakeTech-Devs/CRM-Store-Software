<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('cus_id')->nullable()->unique()->after('id');
        });

        // Backfill existing rows — store_id may be null for old records, default to 0
        $customers = DB::table('customer')->orderBy('id')->get();
        $counters = [];

        foreach ($customers as $customer) {
            $storeId = $customer->store_id ?? 0;
            $counters[$storeId] = ($counters[$storeId] ?? 0) + 1;
            $cusId = 'CUS-' . $storeId . '-' . str_pad($counters[$storeId], 5, '0', STR_PAD_LEFT);
            DB::table('customer')->where('id', $customer->id)->update(['cus_id' => $cusId]);
        }

        // Now make it non-nullable and add composite unique on (store_id, phone)
        Schema::table('customer', function (Blueprint $table) {
            $table->string('cus_id')->nullable(false)->change();
            $table->unique(['store_id', 'phone'], 'customer_store_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropUnique('customer_store_phone_unique');
            $table->dropColumn('cus_id');
        });
    }
};
