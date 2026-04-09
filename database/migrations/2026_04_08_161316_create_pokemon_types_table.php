<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pokemon_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokemon_id')->constrained('pokemon')->onDelete('cascade');
            $table->string('type');
            $table->integer('slot');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemon_types');
    }
};