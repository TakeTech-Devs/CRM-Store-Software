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
        Schema::table('store', function (Blueprint $table) {
            $table->string('dl_number')->nullable()->after('store_address');
            $table->string('helpline_number')->nullable()->after('dl_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store', function (Blueprint $table) {
            $table->dropColumn(['dl_number', 'helpline_number']);
        });
    }
};
