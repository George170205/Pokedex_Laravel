<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pokemon', function (Blueprint $table) {
            $table->id();
            $table->integer('pokedex_id')->unique();
            $table->integer('generation');
            $table->string('name')->unique();
            $table->string('name_es')->nullable();
            $table->string('sprite')->nullable();
            $table->string('sprite_animated')->nullable();
            $table->integer('hp');
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('sp_attack');
            $table->integer('sp_defense');
            $table->integer('speed');
            $table->integer('height');
            $table->integer('weight');
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->string('evolution_chain_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};