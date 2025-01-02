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
        Schema::create('criticables', function (Blueprint $table) {
            $table->id();
            $table->float('rate');
            $table->string('url');
            $table->morphs('criticable');
            $table->timestamp('posted_at');
            $table->foreignId('critic_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criticables');
    }
};
