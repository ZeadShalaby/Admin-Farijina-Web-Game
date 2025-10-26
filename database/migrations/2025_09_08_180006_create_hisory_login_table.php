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
        Schema::create('admin_login_history', function (Blueprint $table) {
            $table->id();

            $table->string('admin_email');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('location')->nullable();
            $table->string('session_id')->nullable();

            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();

            $table->enum('status', ['online', 'offline'])->default('online');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_login_history');
    }
};
