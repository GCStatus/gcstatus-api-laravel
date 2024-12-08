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
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->integer('coins');
            $table->string('mission');
            $table->integer('experience');
            $table->mediumText('description');
            $table->boolean('for_all')->default(true);
            $table->enum('frequency', ['one_time', 'daily', 'weekly', 'monthly', 'yearly'])->default('one_time');
            $table->foreignId('status_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
