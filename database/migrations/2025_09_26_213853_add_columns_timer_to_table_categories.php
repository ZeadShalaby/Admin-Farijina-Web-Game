<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('timer_200')->default(40);
            $table->integer('timer_400')->default(60);
            $table->integer('timer_600')->default(90);
        });

        // تحديث القيم بعد إضافة الأعمدة
        DB::table('categories')->where('no_words', 0)->update([
            'timer_200' => 0,
            'timer_400' => 0,
            'timer_600' => 0,
        ]);

        DB::table('categories')->where('no_words', '!=', 0)->update([
            'timer_200' => 40,
            'timer_400' => 60,
            'timer_600' => 90,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['timer_200', 'timer_400', 'timer_600']);
        });
    }
};
