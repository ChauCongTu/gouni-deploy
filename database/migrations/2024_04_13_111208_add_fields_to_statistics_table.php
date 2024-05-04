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
        Schema::table('statistics', function (Blueprint $table) {
            $table->unsignedBigInteger('most_done_subject')->nullable();
            $table->text('subjects_done_today')->nullable();
            $table->integer('total_questions_done')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            //
        });
    }
};
