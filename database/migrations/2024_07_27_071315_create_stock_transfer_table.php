<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer', function (Blueprint $table) {
            $table->id();
            $table->integer('requested_id')->nullable();
            $table->integer('requester_id')->nullable();
            $table->integer('stock_id')->nullable();
            $table->string('qty')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer');
    }
};
