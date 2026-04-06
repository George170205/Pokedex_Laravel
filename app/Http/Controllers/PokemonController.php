<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function index()
    {
        $pokemon = [
            'Bulbasaur', 'Charmander', 'Squirtle',
            'Caterpie', 'Pidgey', 'Rattata',
            'Pikachu', 'Clefairy', 'Vulpix',
            'Jigglypuff', 'Meowth', 'Psyduck',
        ];

        return view('pokemon.index', compact('pokemon'));
    }

    public function show(string $name)
    {
        return view('pokemon.show', compact('name'));
    }
}
