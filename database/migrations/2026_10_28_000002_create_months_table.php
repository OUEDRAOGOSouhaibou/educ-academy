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
        Schema::create('months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->restrictOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('calendar_number');
            $table->unsignedTinyInteger('position');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['school_year_id', 'calendar_number']);
            $table->index(['school_year_id', 'is_published', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('months');
    }
};
