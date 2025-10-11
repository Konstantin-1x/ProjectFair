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
        Schema::create('team_vacancy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('position'); // название позиции
            $table->text('description')->nullable();
            $table->string('requirements')->nullable();
            $table->string('status')->default('open'); // open, filled, closed
            $table->foreignId('filled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('filled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_vacancy');
    }
};
