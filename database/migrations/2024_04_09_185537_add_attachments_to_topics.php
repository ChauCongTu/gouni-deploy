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
        Schema::table('topics', function (Blueprint $table) {
            $table->string('attachment')->nullable();
        });

        Schema::table('topic_comments', function (Blueprint $table) {
            $table->string('attachment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        Schema::table('topic_comments', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
