<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'remote_mysql';

    public function up(): void
    {
        // Every store's install shares the same admin database but tracks its own
        // migration history independently — guard both creates so whichever store
        // applies this first doesn't cause every other store's update to fail (or,
        // if ever combined with a drop, destroy real data). Same lesson as the
        // credit_note and stock_transfer remote migration fixes earlier.
        if (!Schema::connection('remote_mysql')->hasTable('stock_writeoff')) {
            Schema::connection('remote_mysql')->create('stock_writeoff', function (Blueprint $table) {
                $table->id();
                $table->string('writeoff_no')->unique();
                $table->unsignedBigInteger('store_id');
                $table->date('writeoff_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('remote_mysql')->hasTable('stock_writeoff_items')) {
            Schema::connection('remote_mysql')->create('stock_writeoff_items', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::connection('remote_mysql')->dropIfExists('stock_writeoff_items');
        Schema::connection('remote_mysql')->dropIfExists('stock_writeoff');
    }
};
