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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('category')->default('it'); // it, business, design, research, education, social, other
            $table->string('subcategory')->nullable(); // подкатегория для более детальной классификации
            $table->text('goals')->nullable(); // цели проекта
            $table->text('target_audience')->nullable(); // целевая аудитория
            $table->string('complexity_level')->default('medium'); // easy, medium, hard, expert
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['category', 'subcategory', 'goals', 'target_audience', 'complexity_level']);
        });
    }
};
