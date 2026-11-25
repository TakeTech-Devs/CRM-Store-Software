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
        Schema::table('customer_product_billing', function (Blueprint $table) {
            $table->unsignedBigInteger('inhouse_product_id')->nullable()->after('productId');
        });
    }

    public function down(): void
    {
        Schema::table('customer_product_billing', function (Blueprint $table) {
            $table->dropColumn('inhouse_product_id');
        });
    }
};
