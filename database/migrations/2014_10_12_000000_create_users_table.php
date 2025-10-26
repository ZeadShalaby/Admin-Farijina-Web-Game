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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['admin', 'user'])->default('user');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('password');
            $table->enum('login_type', ['google', 'apple', 'facebook', 'normal'])->default('normal');
            $table->string('image')->nullable();
            $table->string('fcm')->nullable();
            $table->enum('gander', ['male', 'female'])->default('male');
            $table->string('date')->nullable(); // like 2000-01-01
            $table->string('code')->nullable();
            $table->string('note')->nullable();
            $table->boolean('status')->default(true);
            $table->string('invitation_code')->nullable();
            $table->float('wallet')->default(0.00);
            $table->integer('num_of_games')->default(1);
            $table->boolean('is_free')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
