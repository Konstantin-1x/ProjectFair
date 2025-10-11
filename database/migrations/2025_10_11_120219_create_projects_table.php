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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('status')->default('active'); // active, completed, archived
            $table->string('type')->nullable(); // тип проекта
            $table->string('institute')->nullable(); // институт
            $table->integer('course')->nullable(); // курс
            $table->string('image')->nullable(); // изображение проекта
            $table->json('gallery')->nullable(); // галерея изображений
            $table->string('demo_url')->nullable(); // ссылка на демо
            $table->string('github_url')->nullable(); // ссылка на GitHub
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
