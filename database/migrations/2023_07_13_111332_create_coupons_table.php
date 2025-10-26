<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->decimal('value', 8, 2)->nullable(); // For discount coupons (percentage or fixed value)
            $table->enum('type', ['discount', 'free_games'])->default('discount'); // Type of coupon
            $table->enum('discount_type', ['percentage', 'fixed', 'free_shipping', 'bogo'])->nullable(); // Only for discount coupons
            $table->integer('total_games')->nullable(); // Only for free games coupons
            $table->boolean('active')->default(true);
            $table->integer('usage_limit')->default(1); // Total usage limit
            $table->integer('usage_per_user')->nullable()->default(1); // Usage limit per user
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
