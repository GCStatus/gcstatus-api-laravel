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
        Schema::create('cracks', function (Blueprint $table) {
            $table->id();
            $table->date('cracked_at')->nullable();
            $table->foreignId('game_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->foreignId('cracker_id')->constrained();
            $table->foreignId('protection_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cracks');
    }
};
