<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('update_log', function (Blueprint $table) {
            $table->id();
            $table->string('previous_version')->nullable();
            $table->string('new_version');
            $table->string('status'); // pending, success, failed
            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->json('changed_files')->nullable();
            $table->json('migrations_run')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('update_log');
    }
};
