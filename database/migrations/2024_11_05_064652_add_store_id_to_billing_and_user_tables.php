<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToBillingAndUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable(); // Adjust position if needed
        });
        Schema::table('customer_product_billing', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable(); // Adjust position if needed
        });

        Schema::table('staff_product_billing', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
        });

        Schema::table('staff_billing', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });
        Schema::table('customer_product_billing', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });

        Schema::table('staff_product_billing', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });
        Schema::table('staff_billing', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });
    }
}
