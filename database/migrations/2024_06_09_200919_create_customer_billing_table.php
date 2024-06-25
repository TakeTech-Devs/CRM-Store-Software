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
        Schema::create('customer_billing', function (Blueprint $table) {
            $table->id();
            $table->string('customer_phone');
            $table->string('customer_name');
            $table->string('doctor_name');
            $table->string('invoiceNo');
            $table->string('paymentType');
            $table->string('billing_date');
            $table->string('total_amt');
            $table->string('billingType');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_billing');
    }
};
