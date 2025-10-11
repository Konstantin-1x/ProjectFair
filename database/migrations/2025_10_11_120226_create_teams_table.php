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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('recruiting'); // recruiting, active, completed, disbanded
            $table->integer('max_members')->default(5);
            $table->string('institute')->nullable();
            $table->integer('course')->nullable();
            $table->foreignId('leader_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('recruitment_start')->nullable();
            $table->timestamp('recruitment_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
