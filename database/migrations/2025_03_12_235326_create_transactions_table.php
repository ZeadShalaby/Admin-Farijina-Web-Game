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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable();
            $table->string('result')->nullable();
            $table->timestamp('post_date')->nullable();
            $table->string('tran_id')->nullable();
            $table->string('ref')->nullable();
            $table->string('track_id')->nullable();
            $table->string('auth')->nullable();
            $table->string('order_id')->nullable();
            $table->string('requested_order_id')->nullable();
            $table->string('refund_order_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('invoice_id')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->string('receipt_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('amount')->default(0.00);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->integer('num_of_games_he_pay')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
