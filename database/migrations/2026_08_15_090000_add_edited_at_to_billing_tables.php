<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('credit_applied_amt');
        });

        Schema::table('staff_billing', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('credit_applied_amt');
        });
    }

    public function down(): void
    {
        Schema::table('customer_billing', function (Blueprint $table) {
            $table->dropColumn('edited_at');
        });

        Schema::table('staff_billing', function (Blueprint $table) {
            $table->dropColumn('edited_at');
        });
    }
};
