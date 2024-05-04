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
        Schema::create('arenas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('author');
            $table->text('users');
            $table->integer('max_users');
            $table->integer('time');
            $table->text('questions');
            $table->dateTime('start_at');
            $table->string('type', 50);
            $table->string('password', 255)->nullable();
            $table->foreign('author')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arenas');
    }
};
