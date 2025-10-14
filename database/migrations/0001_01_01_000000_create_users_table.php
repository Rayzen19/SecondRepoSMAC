<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('type', ['admin', 'teacher', 'student', 'guardian'])->default('student');
            $table->unsignedBigInteger('user_pk_id')->nullable()->comment('References the primary key ID from the respective user type table');
            $table->rememberToken();
            $table->timestamps();
        });

        // Seed default users: admin, teacher, and student
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@school.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'type' => 'admin',
                'user_pk_id' => 1, // Assuming the admin's PK ID is 1 in the admins table
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'teacher@school.test'],
            [
                'name' => 'Default Teacher',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'type' => 'teacher',
                'user_pk_id' => 1, // Assuming the teacher's PK ID is 1 in the teachers table
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'student@school.test'],
            [
                'name' => 'Default Student',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'type' => 'student',
                'user_pk_id' => 1, // Assuming the student's PK ID is 1 in the students table
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'guardian@school.test'],
            [
                'name' => 'Default Guardian',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'type' => 'guardian',
                'user_pk_id' => 1, // Assuming the guardian's PK ID is 1 in the guardians table
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
