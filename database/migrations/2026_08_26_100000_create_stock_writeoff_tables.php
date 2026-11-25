<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_writeoff', function (Blueprint $table) {
            $table->id();
            $table->string('writeoff_no')->unique();
            $table->unsignedBigInteger('store_id');
            $table->date('writeoff_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('store')->onDelete('cascade');
        });

        Schema::create('stock_writeoff_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('writeoff_id');
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->unsignedBigInteger('pack_id');
            $table->string('pack_name');
            $table->unsignedBigInteger('price_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('unit_value');
            $table->string('qty');
            $table->enum('reason', ['defective', 'broken', 'expired', 'other']);
            $table->timestamps();

            $table->foreign('writeoff_id')->references('id')->on('stock_writeoff')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_writeoff_items');
        Schema::dropIfExists('stock_writeoff');
    }
};
