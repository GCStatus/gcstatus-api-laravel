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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('cover');
            $table->text('about');
            $table->text('description');
            $table->text('short_description');
            $table->integer('age');
            $table->boolean('free')->default(false);
            $table->boolean('great_release')->default(false);
            $table->string('legal')->nullable();
            $table->string('website')->nullable();
            $table->date('release_date');
            $table->enum('condition', ['hot', 'sale', 'popular', 'commom'])->default('commom');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
