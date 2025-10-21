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
        Schema::table('tasks', function (Blueprint $table) {
            // Сначала удаляем внешний ключ
            $table->dropForeign(['assigned_team_id']);
            // Затем удаляем колонки
            $table->dropColumn(['assigned_team_id', 'assigned_at', 'institute', 'course', 'max_team_size']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Добавляем новые поля
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->text('completion_text')->nullable(); // текст выполнения задачи
            $table->string('completion_file')->nullable(); // файл выполнения задачи
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Восстанавливаем старые поля
            $table->foreignId('assigned_team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->string('institute')->nullable();
            $table->integer('course')->nullable();
            $table->integer('max_team_size')->default(5);
            
            // Удаляем новые поля
            $table->dropColumn(['project_id', 'assigned_user_id', 'completion_text', 'completion_file']);
        });
    }
};
