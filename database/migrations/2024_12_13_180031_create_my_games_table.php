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
        Schema::create('my_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('type_of_game');
            $table->string('name_first_player');
            $table->string('name_second_player');
            $table->integer('num_first_player');
            $table->integer('num_second_player');
            $table->integer('num_of_play')->default(1);

            $table->boolean('first_player_no_answer')->default(0);
            $table->boolean('first_player_al_jleeb')->default(0);
            $table->boolean('first_player_tow_answer')->default(0);

            $table->boolean('second_player_no_answer')->default(0);
            $table->boolean('second_player_al_jleeb')->default(0);
            $table->boolean('second_player_tow_answer')->default(0);

            // vertebrae
            // 1- player one
            $table->boolean('first_player_vertebrae_one')->default(0);
            $table->boolean('first_player_vertebrae_two')->default(0);
            // 2- player two
            $table->boolean('second_player_vertebrae_one')->default(0);
            $table->boolean('second_player_vertebrae_two')->default(0);
            // 3- player three
            $table->boolean('is_free')->default(false);


            $table->integer('first_player_points')->default(0);
            $table->integer('second_player_points')->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_games');
    }
};
