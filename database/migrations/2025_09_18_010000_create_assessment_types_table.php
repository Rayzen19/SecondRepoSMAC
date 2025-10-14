<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assessment_types')) {
            return;
        }

        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Written Work
            $table->string('key')->unique(); // e.g. written_work
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_types');
    }
};
