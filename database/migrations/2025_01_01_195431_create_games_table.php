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
            $table->string('slug')->unique()->index();
            $table->string('cover');
            $table->text('about');
            $table->text('description');
            $table->text('short_description');
            $table->integer('age');
            $table->integer('views')->default(0);
            $table->boolean('free')->default(false);
            $table->boolean('great_release')->default(false);
            $table->text('legal')->nullable();
            $table->string('website')->nullable();
            $table->date('release_date');
            $table->enum('condition', ['hot', 'sale', 'popular', 'common'])->default('common');
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
