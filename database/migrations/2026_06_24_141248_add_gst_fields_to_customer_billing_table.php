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
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->string('gst')->nullable()->after('total_amt');
            $table->string('cgst')->nullable()->after('gst');
            $table->string('sgst')->nullable()->after('cgst');
        });
    }

    public function down(): void
    {
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->dropColumn(['gst', 'cgst', 'sgst']);
        });
    }
};
