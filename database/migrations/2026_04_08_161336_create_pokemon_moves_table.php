<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pokemon_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokemon_id')->constrained('pokemon')->onDelete('cascade');
            $table->string('move_name');
            $table->string('move_type')->nullable();
            $table->string('damage_class')->nullable(); // physical, special, status
            $table->integer('power')->nullable();
            $table->integer('accuracy')->nullable();
            $table->string('learn_method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemon_moves');
    }
};