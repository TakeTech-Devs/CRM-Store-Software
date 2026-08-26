<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'remote_mysql';

    public function up(): void
    {
        if (Schema::connection('remote_mysql')->hasColumn('customer_billing', 'total_amt')
            && !Schema::connection('remote_mysql')->hasColumn('customer_billing', 'edited_at')) {
            Schema::connection('remote_mysql')->table('customer_billing', function (Blueprint $table) {
                $table->timestamp('edited_at')->nullable();
            });
        }

        if (Schema::connection('remote_mysql')->hasColumn('staff_billing', 'total_amt')
            && !Schema::connection('remote_mysql')->hasColumn('staff_billing', 'edited_at')) {
            Schema::connection('remote_mysql')->table('staff_billing', function (Blueprint $table) {
                $table->timestamp('edited_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('remote_mysql')->hasColumn('customer_billing', 'edited_at')) {
            Schema::connection('remote_mysql')->table('customer_billing', function (Blueprint $table) {
                $table->dropColumn('edited_at');
            });
        }

        if (Schema::connection('remote_mysql')->hasColumn('staff_billing', 'edited_at')) {
            Schema::connection('remote_mysql')->table('staff_billing', function (Blueprint $table) {
                $table->dropColumn('edited_at');
            });
        }
    }
};
