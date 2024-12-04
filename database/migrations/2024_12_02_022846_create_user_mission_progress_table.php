<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_mission_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('progress');
            $table->boolean('completed')->default(false);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('mission_requirement_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_mission_progress');
    }
};
