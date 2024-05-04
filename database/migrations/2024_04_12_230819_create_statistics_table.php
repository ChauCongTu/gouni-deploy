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
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('total_time')->default(0);
            $table->integer('total_exams')->default(0);
            $table->integer('total_practices')->default(0);
            $table->integer('total_arenas')->default(0);
            $table->text('histories')->nullable();
            $table->integer('min_score')->nullable();
            $table->integer('max_score')->nullable();
            $table->float('avg_score', 8, 2)->nullable();
            $table->integer('late_submissions')->default(0);
            $table->float('accuracy', 8, 2)->nullable();
            $table->timestamp('day_stats')->default(now());
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
