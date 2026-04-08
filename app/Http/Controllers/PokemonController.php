<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    const GENERATIONS = [
        1 => ['label' => 'Generación I',   'limit' => 151, 'offset' => 0],
        2 => ['label' => 'Generación II',  'limit' => 100, 'offset' => 151],
        3 => ['label' => 'Generación III', 'limit' => 135, 'offset' => 251],
    ];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $error  = null;
        $pokemon = [];
        $byType  = [];
        $types   = [];

        if ($request->isMethod('post') && trim($search) === '') {
            $error = 'El campo de búsqueda no puede estar vacío.';
            return view('pokemon.index', compact('pokemon', 'search', 'error', 'byType', 'types'));
        }

        if ($search) {
            return redirect()->route('pokemon.show', strtolower(trim($search)));
        }

        $gen = (int) $request->input('gen', 1);
        if (!array_key_exists($gen, self::GENERATIONS)) $gen = 1;
        $genData = self::GENERATIONS[$gen];

        $filterType = $request->input('type', '');

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
                ->get('https://pokeapi.co/api/v2/pokemon', [
                    'limit'  => $genData['limit'],
                    'offset' => $genData['offset'],
                ]);

            if ($response->successful()) {
                $results = $response->json()['results'];

                // Fetch detalles de cada pokémon en paralelo (por lotes de 30)
                $chunks = array_chunk($results, 30);
                foreach ($chunks as $chunk) {
                    $promises = [];
                    foreach ($chunk as $item) {
                        $detail = Http::timeout(8)
                            ->withoutVerifying()
                            ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
                            ->get($item['url']);

                        if ($detail->successful()) {
                            $d = $detail->json();
                            $pokemon[] = [
                                'name'   => $d['name'],
                                'id'     => $d['id'],
                                'sprite' => $d['sprites']['other']['official-artwork']['front_default']
                                            ?? $d['sprites']['front_default']
                                            ?? null,
                                'types'  => array_map(fn($t) => $t['type']['name'], $d['types']),
                            ];
                        }
                    }
                }

                // Recolectar todos los tipos únicos
                foreach ($pokemon as $p) {
                    foreach ($p['types'] as $t) {
                        $types[$t] = $t;
                    }
                }
                ksort($types);

                // Filtrar por tipo si aplica
                if ($filterType && isset($types[$filterType])) {
                    $pokemon = array_filter($pokemon, fn($p) => in_array($filterType, $p['types']));
                }

                // Agrupar por tipo primario
                foreach ($pokemon as $p) {
                    $primaryType = $p['types'][0];
                    $byType[$primaryType][] = $p;
                }
                ksort($byType);

            } else {
                $error = 'No se pudo conectar con la PokéAPI. Intenta más tarde.';
            }
        } catch (\Exception $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }

        return view('pokemon.index', compact('pokemon', 'search', 'error', 'byType', 'types', 'gen', 'filterType'));
    }

    public function show(string $name)
    {
        $pokemon = null;
        $error   = null;

        try {
            $response = Http::timeout(8)
                ->withoutVerifying()
                ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
                ->get("https://pokeapi.co/api/v2/pokemon/{$name}");

            if ($response->successful()) {
                $data    = $response->json();
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