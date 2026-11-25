<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->string('credit_note_no')->nullable()->after('total_amt');
            $table->string('credit_applied_amt')->nullable()->after('credit_note_no');
        });

        Schema::table('staff_billing', function (Blueprint $table) {
            $table->string('credit_note_no')->nullable()->after('total_amt');
            $table->string('credit_applied_amt')->nullable()->after('credit_note_no');
        });
    }

    public function down(): void
    {
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->dropColumn(['credit_note_no', 'credit_applied_amt']);
        });

        Schema::table('staff_billing', function (Blueprint $table) {
            $table->dropColumn(['credit_note_no', 'credit_applied_amt']);
        });
    }
};
