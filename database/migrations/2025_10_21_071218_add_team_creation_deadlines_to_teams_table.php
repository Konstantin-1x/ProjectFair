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
        Schema::table('teams', function (Blueprint $table) {
            $table->timestamp('team_creation_deadline')->nullable(); // срок создания команды
            $table->timestamp('team_formation_deadline')->nullable(); // срок набора команды
            $table->boolean('is_team_formation_closed')->default(false); // закрыт ли набор в команду
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['team_creation_deadline', 'team_formation_deadline', 'is_team_formation_closed']);
        });
    }
};
