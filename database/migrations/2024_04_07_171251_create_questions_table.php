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
            $table->text('question');
            $table->text('answer_1')->nullable();
            $table->text('answer_2')->nullable();
            $table->text('answer_3')->nullable();
            $table->text('answer_4')->nullable();
            $table->integer('answer_correct')->nullable();
            $table->text('answer_detail')->nullable();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('chapter_id');
            $table->integer('level')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
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
