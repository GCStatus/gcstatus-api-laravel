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
        Schema::create('requirementables', function (Blueprint $table) {
            $table->id();
            $table->string('os');
            $table->string('dx');
            $table->string('cpu');
            $table->string('ram');
            $table->string('gpu');
            $table->string('rom');
            $table->string('network');
            $table->string('obs')->nullable();
            $table->morphs('requirementable');
            $table->foreignId('requirement_type_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirementables');
    }
};
