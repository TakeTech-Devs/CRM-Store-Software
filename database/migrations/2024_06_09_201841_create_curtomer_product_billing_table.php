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
        Schema::create('curtomer_product_billing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cb_id');
            $table->string('category');
            $table->string('discount');
            $table->string('mrp');
            $table->string('pack');
            $table->string('productId');
            $table->string('qty');
            $table->string('subCategory');
            $table->string('totalAmount');
            $table->string('unitValue');
            $table->foreign('cb_id')
            ->references('id')
            ->on('curtomer_billing')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curtomer_product_billing');
    }
};
