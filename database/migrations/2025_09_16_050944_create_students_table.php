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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('birthdate');
            $table->string('email')->unique();
            $table->string('mobile_number')->unique();
            $table->text('address')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_contact')->unique();
            $table->string('guardian_email')->unique();
            $table->string('program');
            $table->string('academic_year');
            $table->unsignedBigInteger('academic_year_id');
            $table->enum('status', ['active', 'graduated', 'dropped'])->default('active');
            $table->string('profile_picture')->nullable();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
