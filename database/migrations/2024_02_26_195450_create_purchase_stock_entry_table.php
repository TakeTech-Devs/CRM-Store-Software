<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_stock_entry', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_stock_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('pack_id');
            $table->unsignedBigInteger('price_id');

            $table->foreign('purchase_stock_id')
                ->references('id')
                ->on('purchase_stock')
                ->onDelete('cascade');

            $table->foreign('brand_id')
                ->references('id')
                ->on('brand')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('category')
                ->onDelete('cascade');

            $table->foreign('sub_category_id')
                ->references('id')
                ->on('sub_category')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->onDelete('cascade');

            $table->foreign('pack_id')
                ->references('id')
                ->on('pack')
                ->onDelete('cascade');

            $table->foreign('price_id')
                ->references('id')
                ->on('price')
                ->onDelete('cascade');

            $table->string('qty');
            $table->date('exp_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_stock_entry');
    }
};
