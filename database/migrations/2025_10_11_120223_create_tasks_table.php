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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable(); // требования к решению
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->string('status')->default('open'); // open, in_progress, completed, closed
            $table->string('type')->nullable(); // тип задачи
            $table->string('institute')->nullable(); // институт
            $table->integer('course')->nullable(); // курс
            $table->integer('max_team_size')->default(5); // максимальный размер команды
            $table->timestamp('deadline')->nullable(); // срок выполнения
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
