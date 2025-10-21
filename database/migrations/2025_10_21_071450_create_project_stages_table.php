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
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name'); // название этапа
            $table->text('description')->nullable(); // описание этапа
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->integer('order')->default(0); // порядок этапа
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->text('notes')->nullable(); // заметки по этапу
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stages');
    }
};
