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
            $table->timestamp('project_deadline')->nullable(); // общий срок выполнения проекта
            $table->timestamp('team_join_deadline')->nullable(); // срок входа команд в проект
            $table->timestamp('task_submission_deadline')->nullable(); // срок сдачи задач
            $table->boolean('is_deadline_extended')->default(false); // был ли продлен срок
            $table->text('deadline_extension_reason')->nullable(); // причина продления
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'project_deadline',
                'team_join_deadline', 
                'task_submission_deadline',
                'is_deadline_extended',
                'deadline_extension_reason'
            ]);
        });
    }
};
