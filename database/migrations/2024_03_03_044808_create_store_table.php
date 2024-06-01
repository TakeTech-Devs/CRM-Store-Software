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
        Schema::create('store', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('store_address')->nullable();
            $table->string('store_mail')->nullable();
            $table->string('store_start_date')->nullable();
            $table->string('store_meta_id')->nullable();
            $table->string('store_pass_key')->nullable();
            $table->string('store_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store');
    }
};
