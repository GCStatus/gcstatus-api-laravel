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
        Schema::create('game_supports', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->foreignId('game_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_supports');
    }
};
