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
        Schema::create('team_project_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('applied_by')->constrained('users')->onDelete('cascade'); // Кто подал заявку (лидер команды)
            $table->text('message')->nullable(); // Сообщение от лидера команды
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('applied_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Уникальность: одна команда может подать только одну заявку на проект
            $table->unique(['team_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_project_applications');
    }
};
