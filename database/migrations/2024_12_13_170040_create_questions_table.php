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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('points');
            $table->text('question');
            $table->text('answer');
            $table->text('link_question')->nullable();
            $table->text('link_answer')->nullable();
            $table->enum('link_type', ['video', 'image', 'voice', 'text'])->default('text')->nullable(); // link_question 
            $table->enum('link_answer_type', ['video', 'image', 'voice', 'text'])->default('text')->nullable(); // link_question 

            $table->integer('views')->default(0);
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->boolean('is_active')->default(1);
            $table->boolean('is_free')->default(0);
            $table->string('type')->default("yamaat"); // yamaat / horror / vertebrae / luck 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
