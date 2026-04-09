<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;
use App\Models\PokemonType;
use App\Models\PokemonMove;

class PokemonController extends Controller
{
    const GENERATIONS = [
        1 => ['label' => 'Generación I',   'limit' => 151, 'offset' => 0],
        2 => ['label' => 'Generación II',  'limit' => 100, 'offset' => 151],
        3 => ['label' => 'Generación III', 'limit' => 135, 'offset' => 251],
    ];

    public function index(Request $request)
    {
        $search     = $request->input('search');
        $error      = null;
        $byType     = [];
        $types      = [];
        $pokemon    = [];

        if ($request->isMethod('post') && trim($search) === '') {
            $error = 'El campo de búsqueda no puede estar vacío.';
            return view('pokemon.index', compact('pokemon', 'search', 'error', 'byType', 'types'));
        }

        if ($search) {
            return redirect()->route('pokemon.show', strtolower(trim($search)));
        }

        $gen        = (int) $request->input('gen', 1);
        if (!array_key_exists($gen, self::GENERATIONS)) $gen = 1;
        $filterType = $request->input('type', '');

        // Buscar en DB
        $query = Pokemon::with('types')->where('generation', $gen);

        if ($filterType) {
            $query->whereHas('types', fn($q) => $q->where('type', $filterType));
        }

        $pokemon = $query->orderBy('pokedex_id')->get();

        // Si no hay datos en DB, sincronizar desde API
        if ($pokemon->isEmpty()) {
            $this->syncGenerationFromApi($gen);
            $pokemon = Pokemon::with('types')->where('generation', $gen)->orderBy('pokedex_id')->get();
        }

        // Tipos únicos para los filtros
        $types = PokemonType::whereIn('pokemon_id', $pokemon->pluck('id'))
            ->distinct()->pluck('type')->sort()->values();

        // Agrupar por tipo primario
        foreach ($pokemon as $p) {
            $primaryType = $p->types->first()->type ?? 'normal';
            $byType[$primaryType][] = $p;
        }
        ksort($byType);

        return view('pokemon.index', compact('pokemon', 'search', 'error', 'byType', 'types', 'gen', 'filterType'));
    }

    public function show(string $name)
    {
        $error   = null;
        $pokemon = null;

        // Buscar en DB primero
        $db = Pokemon::with(['types', 'moves'])->where('name', strtolower($name))->first();

        if ($db) {
            $pokemon = $this->formatFromDb($db);
        } else {
            // No está en DB, traer de API y guardar
            $pokemon = $this->fetchAndSaveFromApi($name);
            if (!$pokemon) {
                $error = "Pokémon \"$name\" no encontrado.";
            }
        }

        // Cadena de evolución
        $evolutions = [];
        if ($db && $db->evolution_chain_url) {
            $evolutions = $this->getEvolutionChain($db->evolution_chain_url);
        } elseif ($pokemon && isset($pokemon['evolution_chain_url'])) {
            $evolutions = $this->getEvolutionChain($pokemon['evolution_chain_url']);
        }

        // Debilidades
        $weaknesses = $pokemon ? $this->getWeaknesses($pokemon['types']) : [];

        return view('pokemon.show', compact('pokemon', 'name', 'error', 'evolutions', 'weaknesses'));
    }

    public function about()
    {
        return view('about');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function formatFromDb(Pokemon $db): array
    {
        return [
            'id'                  => $db->pokedex_id,
            'name'                => $db->name,
            'name_es'             => $db->name_es,
            'sprite'              => $db->sprite,
            'sprite_animated'     => $db->sprite_animated,
            'types'               => $db->types->pluck('type')->toArray(),
            'hp'                  => $db->hp,
            'attack'              => $db->attack,
            'defense'             => $db->defense,
            'sp_attack'           => $db->sp_attack,
            'sp_defense'          => $db->sp_defense,
            'speed'               => $db->speed,
            'height'              => $db->height,
            'weight'              => $db->weight,
            'color'               => $db->color,
            'description'         => $db->description,
            'evolution_chain_url' => $db->evolution_chain_url,
            'moves'               => $db->moves->map(fn($m) => [
                'name'         => $m->move_name,
                'type'         => $m->move_type,
                'damage_class' => $m->damage_class,
                'power'        => $m->power,
                'accuracy'     => $m->accuracy,
            ])->toArray(),
        ];
    }

    private function fetchAndSaveFromApi(string $name): ?array
    {
        try {
            $response = Http::withOptions([
                'verify'  => false,
                'headers' => ['User-Agent' => 'pokedex-laravel/1.0', 'Accept' => 'application/json'],
            ])->timeout(10)->get("https://pokeapi.co/api/v2/pokemon/{$name}");

            if (!$response->successful()) return null;

            $d = $response->json();

            $spec = Http::withOptions(['verify' => false])->timeout(10)->get($d['species']['url'])->json();

            $description = collect($spec['flavor_text_entries'] ?? [])
                ->firstWhere('language.name', 'es')['flavor_text']
                ?? collect($spec['flavor_text_entries'] ?? [])->firstWhere('language.name', 'en')['flavor_text']
                ?? null;

            $description = $description
                ? preg_replace('/\s+/', ' ', str_replace(["\n", "\f"], ' ', $description))
                : null;

            $gen = $this->getGenFromId($d['id']);

            $db = Pokemon::updateOrCreate(['pokedex_id' => $d['id']], [
                'generation'          => $gen,
                'name'                => $d['name'],
                'name_es'             => collect($spec['names'] ?? [])->firstWhere('language.name', 'es')['name'] ?? $d['name'],
                'sprite'              => $d['sprites']['other']['official-artwork']['front_default'] ?? $d['sprites']['front_default'] ?? null,
                'sprite_animated'     => $d['sprites']['versions']['generation-v']['black-white']['animated']['front_default'] ?? null,
                'hp'                  => $d['stats'][0]['base_stat'],
                'attack'              => $d['stats'][1]['base_stat'],
                'defense'             => $d['stats'][2]['base_stat'],
                'sp_attack'           => $d['stats'][3]['base_stat'],
                'sp_defense'          => $d['stats'][4]['base_stat'],
                'speed'               => $d['stats'][5]['base_stat'],
                'height'              => $d['height'],
                'weight'              => $d['weight'],
                'color'               => $spec['color']['name'] ?? null,
                'description'         => $description,
                'evolution_chain_url' => $spec['evolution_chain']['url'] ?? null,
            ]);

            $db->types()->delete();
            foreach ($d['types'] as $t) {
                PokemonType::create(['pokemon_id' => $db->id, 'type' => $t['type']['name'], 'slot' => $t['slot']]);
            }

            return $this->formatFromDb($db->load(['types', 'moves']));

        } catch (\Exception $e) {
            return null;
        }
    }

    private function getEvolutionChain(string $url): array
    {
        try {
            $data  = Http::withOptions(['verify' => false])->timeout(10)->get($url)->json();
            $chain = $data['chain'] ?? null;
            if (!$chain) return [];

            $evolutions = [];
            $current    = $chain;

            while ($current) {
                $pokeName = $current['species']['name'];
                $db       = Pokemon::with('types')->where('name', $pokeName)->first();

                $evolutions[] = [
                    'name'   => $pokeName,
                    'name_es'=> $db->name_es ?? $pokeName,
                    'sprite' => $db->sprite ?? null,
                    'types'  => $db ? $db->types->pluck('type')->toArray() : [],
                ];

                $current = $current['evolves_to'][0] ?? null;
            }

            return $evolutions;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getWeaknesses(array $types): array
    {
        $weaknesses = [
            'normal'   => ['fighting'],
            'fire'     => ['water', 'ground', 'rock'],
            'water'    => ['electric', 'grass'],
            'grass'    => ['fire', 'ice', 'poison', 'flying', 'bug'],
            'electric' => ['ground'],
            'ice'      => ['fire', 'fighting', 'rock', 'steel'],
            'fighting' => ['flying', 'psychic', 'fairy'],
            'poison'   => ['ground', 'psychic'],
            'ground'   => ['water', 'grass', 'ice'],
            'flying'   => ['electric', 'ice', 'rock'],
            'psychic'  => ['bug', 'ghost', 'dark'],
            'bug'      => ['fire', 'flying', 'rock'],
            'rock'     => ['water', 'grass', 'fighting', 'ground', 'steel'],
            'ghost'    => ['ghost', 'dark'],
            'dragon'   => ['ice', 'dragon', 'fairy'],
            'dark'     => ['fighting', 'bug', 'fairy'],
            'steel'    => ['fire', 'fighting', 'ground'],
            'fairy'    => ['poison', 'steel'],
        ];

        $result = [];
        foreach ($types as $type) {
            foreach ($weaknesses[$type] ?? [] as $w) {
                $result[$w] = $w;
            }
        }
        return array_values($result);
    }

    private function getGenFromId(int $id): int
    {
        if ($id <= 151) return 1;
        if ($id <= 251) return 2;
        return 3;
    }

    private function syncGenerationFromApi(int $gen): void
    {
        try {
            $genData = self::GENERATIONS[$gen];
            $list    = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->get('https://pokeapi.co/api/v2/pokemon', [
                    'limit'  => $genData['limit'],
                    'offset' => $genData['offset'],
                ])->json()['results'] ?? [];

            foreach ($list as $item) {
                $this->fetchAndSaveFromApi($item['name']);
            }
        } catch (\Exception $e) {
            // Silencioso, la vista mostrará lo que haya
        }
    }
}