<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'remote_mysql';

    public function up(): void
    {
        // Drop old incompatible tables on admin DB
        Schema::connection('remote_mysql')->dropIfExists('stock_transfer_items');
        Schema::connection('remote_mysql')->dropIfExists('stock_transfer');

        Schema::connection('remote_mysql')->create('stock_transfer', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_no')->unique();
            $table->unsignedBigInteger('from_store_id');
            $table->unsignedBigInteger('to_store_id');
            $table->date('transfer_date');
            $table->enum('status', ['pending', 'received'])->default('pending');
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::connection('remote_mysql')->create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_id');
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->unsignedBigInteger('pack_id');
            $table->string('pack_name');
            $table->unsignedBigInteger('price_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('unit_value');
            $table->string('qty');
            $table->foreign('transfer_id')->references('id')->on('stock_transfer')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('remote_mysql')->dropIfExists('stock_transfer_items');
        Schema::connection('remote_mysql')->dropIfExists('stock_transfer');
    }
};
