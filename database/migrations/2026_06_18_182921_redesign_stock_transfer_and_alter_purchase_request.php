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
        // 1. Recreate stock_transfer with proper schema
        Schema::dropIfExists('stock_transfer');
        Schema::create('stock_transfer', function (Blueprint $table) {
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

        // 2. Create stock_transfer_items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
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

        // 3. Make purchase_request.store_assign_id nullable and add transfer_id
        DB::statement('ALTER TABLE purchase_request DROP FOREIGN KEY purchase_request_store_assign_id_foreign');
        DB::statement('ALTER TABLE purchase_request MODIFY store_assign_id BIGINT UNSIGNED NULL');
        Schema::table('purchase_request', function (Blueprint $table) {
            $table->unsignedBigInteger('transfer_id')->nullable()->after('store_assign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfer');
        Schema::table('purchase_request', function (Blueprint $table) {
            $table->dropColumn('transfer_id');
        });
        DB::statement('ALTER TABLE purchase_request MODIFY store_assign_id BIGINT UNSIGNED NOT NULL');
    }
};
