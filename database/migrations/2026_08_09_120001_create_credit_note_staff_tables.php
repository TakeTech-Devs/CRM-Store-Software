<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_note_staff', function (Blueprint $table) {
            $table->id();
            $table->string('credit_note_no')->unique();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('staff_phone');
            $table->string('staff_name')->nullable();
            $table->unsignedBigInteger('source_bill_id');
            $table->string('source_invoice_no')->nullable();
            $table->string('total_credit_amt');
            $table->enum('status', ['active', 'redeemed'])->default('active');
            $table->string('redeemed_bill_type')->nullable();
            $table->unsignedBigInteger('redeemed_bill_id')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->date('return_date');
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('store')->onDelete('cascade');
        });

        Schema::create('credit_note_staff_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_note_id');
            $table->unsignedBigInteger('source_item_id');
            $table->string('productId')->nullable();
            $table->unsignedBigInteger('inhouse_product_id')->nullable();
            $table->string('pack');
            $table->string('qty');
            $table->string('unitValue');
            $table->string('totalAmount');
            $table->string('gstRate')->nullable();
            $table->string('gstAmount')->nullable();
            $table->timestamps();

            $table->foreign('credit_note_id')->references('id')->on('credit_note_staff')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_note_staff_items');
        Schema::dropIfExists('credit_note_staff');
    }
};
