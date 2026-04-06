<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $error = null;
        $pokemon = [];

        // Validación: no permitir búsqueda vacía
        if ($request->isMethod('post') && trim($search) === '') {
            $error = 'El campo de búsqueda no puede estar vacío.';
            return view('pokemon.index', compact('pokemon', 'search', 'error'));
        }

        // Si hay búsqueda, redirigir al detalle
        if ($search) {
            return redirect()->route('pokemon.show', strtolower(trim($search)));
        }

        // Consumir PokéAPI
        try {
            $response = Http::timeout(8)
    ->withoutVerifying()
    ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
    ->get('https://pokeapi.co/api/v2/pokemon?limit=20');
            if ($response->successful()) {
                $pokemon = $response->json()['results'];
            } else {
                $error = 'No se pudo conectar con la PokéAPI. Intenta más tarde.';
            }
        } catch (\Exception $e) {
            $error = 'Error de conexión con la PokéAPI.';
        }

        return view('pokemon.index', compact('pokemon', 'search', 'error'));
    }

    public function show(string $name)
    {
        $pokemon = null;
        $error = null;

        try {
           $response = Http::timeout(8)
    ->withoutVerifying()
    ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
    ->get("https://pokeapi.co/api/v2/pokemon/{$name}");
            if ($response->successful()) {
                $data = $response->json();
                $pokemon = [
                    'name'    => $data['name'],
                    'sprite'  => $data['sprites']['other']['official-artwork']['front_default']
                                 ?? $data['sprites']['front_default']
                                 ?? null,
                    'types'   => array_map(fn($t) => $t['type']['name'], $data['types']),
                    'hp'      => $data['stats'][0]['base_stat'],
                    'attack'  => $data['stats'][1]['base_stat'],
                    'defense' => $data['stats'][2]['base_stat'],
                    'height'  => $data['height'],
                    'weight'  => $data['weight'],
                ];
            } else {
                $error = "Pokémon \"$name\" no encontrado.";
            }
        } catch (\Exception $e) {
            $error = 'Error de conexión con la PokéAPI.';
        }

        return view('pokemon.show', compact('pokemon', 'name', 'error'));
    }

    public function about()
    {
        return view('about');
    }
}
