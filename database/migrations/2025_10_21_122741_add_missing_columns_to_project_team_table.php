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
        Schema::table('project_team', function (Blueprint $table) {
            // Проверяем, существует ли колонка, и добавляем только если её нет
            if (!Schema::hasColumn('project_team', 'deadline')) {
                $table->timestamp('deadline')->nullable();
            }
            if (!Schema::hasColumn('project_team', 'role_description')) {
                $table->text('role_description')->nullable();
            }
            if (!Schema::hasColumn('project_team', 'status')) {
                $table->enum('status', ['active', 'completed', 'withdrawn'])->default('active');
            }
            if (!Schema::hasColumn('project_team', 'joined_at')) {
                $table->timestamp('joined_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_team', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'role_description', 'status', 'joined_at']);
        });
    }
};