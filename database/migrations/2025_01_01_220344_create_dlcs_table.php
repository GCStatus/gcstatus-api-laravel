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
        Schema::create('dlcs', function (Blueprint $table) {
            $table->id();
            $table->text('about');
            $table->string('title');
            $table->string('cover');
            $table->text('description');
            $table->text('short_description');
            $table->text('legal')->nullable();
            $table->date('release_date');
            $table->boolean('free')->default(false);
            $table->string('slug')->unique()->index();
            $table->foreignId('game_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dlcs');
    }
};
