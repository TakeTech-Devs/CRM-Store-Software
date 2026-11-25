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
        Schema::table('staff_product_billing', function (Blueprint $table) {
            $table->string('productId')->nullable()->change();
            $table->unsignedBigInteger('inhouse_product_id')->nullable()->after('productId');
        });
    }

    public function down(): void
    {
        Schema::table('staff_product_billing', function (Blueprint $table) {
            $table->string('productId')->nullable(false)->change();
            $table->dropColumn('inhouse_product_id');
        });
    }
};
