<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'remote_mysql';

    public function up(): void
    {
        Schema::connection('remote_mysql')->create('credit_note_customer', function (Blueprint $table) {
            $table->id();
            $table->string('credit_note_no')->unique();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('customer_phone');
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('source_bill_id');
            $table->string('source_invoice_no')->nullable();
            $table->string('total_credit_amt');
            $table->enum('status', ['active', 'redeemed'])->default('active');
            $table->string('redeemed_bill_type')->nullable();
            $table->unsignedBigInteger('redeemed_bill_id')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->date('return_date');
            $table->timestamps();
        });

        Schema::connection('remote_mysql')->create('credit_note_customer_items', function (Blueprint $table) {
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
            $table->foreign('credit_note_id')->references('id')->on('credit_note_customer')->onDelete('cascade');
        });

        Schema::connection('remote_mysql')->create('credit_note_staff', function (Blueprint $table) {
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
        });

        Schema::connection('remote_mysql')->create('credit_note_staff_items', function (Blueprint $table) {
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

        if (Schema::connection('remote_mysql')->hasColumn('customer_billing', 'total_amt')
            && !Schema::connection('remote_mysql')->hasColumn('customer_billing', 'credit_note_no')) {
            Schema::connection('remote_mysql')->table('customer_billing', function (Blueprint $table) {
                $table->string('credit_note_no')->nullable()->after('total_amt');
                $table->string('credit_applied_amt')->nullable()->after('credit_note_no');
            });
        }

        if (Schema::connection('remote_mysql')->hasColumn('staff_billing', 'total_amt')
            && !Schema::connection('remote_mysql')->hasColumn('staff_billing', 'credit_note_no')) {
            Schema::connection('remote_mysql')->table('staff_billing', function (Blueprint $table) {
                $table->string('credit_note_no')->nullable()->after('total_amt');
                $table->string('credit_applied_amt')->nullable()->after('credit_note_no');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('remote_mysql')->dropIfExists('credit_note_customer_items');
        Schema::connection('remote_mysql')->dropIfExists('credit_note_customer');
        Schema::connection('remote_mysql')->dropIfExists('credit_note_staff_items');
        Schema::connection('remote_mysql')->dropIfExists('credit_note_staff');

        if (Schema::connection('remote_mysql')->hasColumn('customer_billing', 'credit_note_no')) {
            Schema::connection('remote_mysql')->table('customer_billing', function (Blueprint $table) {
                $table->dropColumn(['credit_note_no', 'credit_applied_amt']);
            });
        }

        if (Schema::connection('remote_mysql')->hasColumn('staff_billing', 'credit_note_no')) {
            Schema::connection('remote_mysql')->table('staff_billing', function (Blueprint $table) {
                $table->dropColumn(['credit_note_no', 'credit_applied_amt']);
            });
        }
    }
};
