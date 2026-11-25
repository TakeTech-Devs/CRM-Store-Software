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
        Schema::table('staff_billing', function (Blueprint $table) {
            $table->string('billingType')->nullable()->after('total_amt');
            $table->string('gst')->nullable()->after('billingType');
            $table->string('cgst')->nullable()->after('gst');
            $table->string('sgst')->nullable()->after('cgst');
        });

        Schema::table('staff_product_billing', function (Blueprint $table) {
            $table->string('gstRate')->nullable()->after('unitValue');
            $table->string('gstAmount')->nullable()->after('gstRate');
        });
    }

    public function down(): void
    {
        Schema::table('staff_billing', function (Blueprint $table) {
            $table->dropColumn(['billingType', 'gst', 'cgst', 'sgst']);
        });

        Schema::table('staff_product_billing', function (Blueprint $table) {
            $table->dropColumn(['gstRate', 'gstAmount']);
        });
    }
};
