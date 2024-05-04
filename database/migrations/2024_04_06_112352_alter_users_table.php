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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('id');
            $table->string('phone', 20)->nullable()->after('password');
            $table->string('invite_code', 50)->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('invite_code');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('avatar');
            $table->date('dob')->nullable()->after('gender');
            $table->string('address')->nullable()->after('dob');
            $table->string('school')->nullable()->after('address');
            $table->string('class', 50)->nullable()->after('school');
            $table->string('test_class', 50)->nullable()->after('class');
            $table->integer('grade')->nullable()->after('test_class');
            $table->timestamp('lastLoginAt')->nullable()->after('grade');
            $table->string('google_id')->nullable()->after('lastLoginAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'invite_code', 'avatar', 'gender', 'dob', 'address', 'school', 'class', 'test_class', 'grade', 'lastLoginAt', 'google_id']);
        });
    }
};
