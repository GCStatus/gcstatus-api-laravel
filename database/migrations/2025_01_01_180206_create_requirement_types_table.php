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
        Schema::create('requirement_types', function (Blueprint $table) {
            $table->id();
            $table->enum('os', ['windows', 'linux', 'mac']);
            $table->enum('potential', ['minimum', 'recommended', 'maximum']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_types');
    }
};
