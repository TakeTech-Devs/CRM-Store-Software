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
        Schema::create('store_assign', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('purchase_stock_id');
            $table->unsignedBigInteger('store_id');
            $table->string('assign_bill_number');
            $table->string('total');



            // $table->foreign('purchase_stock_id')
            //     ->references('id')
            //     ->on('purchase_stock')
            //     ->onDelete('cascade');
            $table->foreign('store_id')
                ->references('id')
                ->on('store')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_assign');
    }
};
