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
        Schema::create('purchase_request', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('store_assign_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('pack_id');
            $table->unsignedBigInteger('price_id');
            $table->string('qty');
            $table->string('qty_left');
            $table->date('exp_date');


            $table->foreign('store_assign_id')
                ->references('id')
                ->on('store_assign')
                ->onDelete('cascade');
            $table->foreign('brand_id')
                ->references('id')
                ->on('brand')
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

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request');
    }
};
