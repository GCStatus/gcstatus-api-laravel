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
        Schema::create('storeables', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->integer('price');
            $table->morphs('storeable');
            $table->string('store_item_id');
            $table->foreignId('store_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storeables');
    }
};
