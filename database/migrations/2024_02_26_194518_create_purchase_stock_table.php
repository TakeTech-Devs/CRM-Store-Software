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
        Schema::create('purchase_stock', function (Blueprint $table) {
            $table->id();
            $table->date('sku_date');
            $table->string('sku_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('purchase_bill_number');
            $table->string('total');
            $table->foreign('supplier_id')
                ->references('id')
                ->on('supplier')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_stock');
    }
};
