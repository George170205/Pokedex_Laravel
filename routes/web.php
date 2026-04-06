<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

Route::get('/', function () {
    return view('home');
});

Route::match(['get', 'post'], '/pokemon', [PokemonController::class, 'index'])->name('pokemon.index');
Route::get('/pokemon/{name}', [PokemonController::class, 'show'])->name('pokemon.show');
Route::get('/about', [PokemonController::class, 'about'])->name('about');
