<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;
use App\Models\PokemonType;
use App\Models\PokemonMove;

class SyncPokemon extends Command
{
    protected $signature   = 'pokemon:sync {--gen=1 : Generación a sincronizar (1, 2 o 3)}';
    protected $description = 'Sincroniza pokémon de la PokéAPI a la base de datos';

    const GENERATIONS = [
        1 => ['limit' => 151, 'offset' => 0,   'number' => 1],
        2 => ['limit' => 100, 'offset' => 151,  'number' => 2],
        3 => ['limit' => 135, 'offset' => 251,  'number' => 3],
    ];

    const TYPE_WEAKNESSES = [
        'normal'   => ['fighting'],
        'fire'     => ['water','ground','rock'],
        'water'    => ['electric','grass'],
        'grass'    => ['fire','ice','poison','flying','bug'],
        'electric' => ['ground'],
        'ice'      => ['fire','fighting','rock','steel'],
        'fighting' => ['flying','psychic','fairy'],
        'poison'   => ['ground','psychic'],
        'ground'   => ['water','grass','ice'],
        'flying'   => ['electric','ice','rock'],
        'psychic'  => ['bug','ghost','dark'],
        'bug'      => ['fire','flying','rock'],
        'rock'     => ['water','grass','fighting','ground','steel'],
        'ghost'    => ['ghost','dark'],
        'dragon'   => ['ice','dragon','fairy'],
        'dark'     => ['fighting','bug','fairy'],
        'steel'    => ['fire','fighting','ground'],
        'fairy'    => ['poison','steel'],
    ];

    public function handle()
    {
        $genNum = (int) $this->option('gen');
        if (!array_key_exists($genNum, self::GENERATIONS)) {
            $this->error("Generación inválida. Usa 1, 2 o 3.");
            return;
        }

        $gen = self::GENERATIONS[$genNum];
        $this->info("Sincronizando Generación {$genNum} ({$gen['limit']} pokémon)...");

        $response = Http::withoutVerifying()
            ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
            ->timeout(15)
            ->get('https://pokeapi.co/api/v2/pokemon', [
                'limit'  => $gen['limit'],
                'offset' => $gen['offset'],
            ]);

        if (!$response->successful()) {
            $this->error("No se pudo conectar con la PokéAPI. Status: " . $response->status());
            return;
        }

        $data = $response->json();

        if (!isset($data['results'])) {
            $this->error("Respuesta inesperada de la API: " . json_encode($data));
            return;
        }

        $list = $data['results'];
        $this->info("Encontrados " . count($list) . " pokémon. Iniciando sincronización...");

        $bar = $this->output->createProgressBar(count($list));
        $bar->start();

        foreach ($list as $item) {
            $this->syncOne($item['url'], $genNum);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Generación {$genNum} sincronizada.");
    }

    private function syncOne(string $url, int $generation)
    {
        try {
            $d = Http::withoutVerifying()
                ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
                ->timeout(10)->get($url)->json();

            // Descripción en español
            $speciesUrl = $d['species']['url'];
            $spec = Http::withoutVerifying()
                ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
                ->timeout(10)->get($speciesUrl)->json();

            $description = collect($spec['flavor_text_entries'] ?? [])
                ->firstWhere('language.name', 'es')['flavor_text']
                ?? collect($spec['flavor_text_entries'] ?? [])
                    ->firstWhere('language.name', 'en')['flavor_text']
                ?? null;

            $description = $description
                ? preg_replace('/\s+/', ' ', str_replace(["\n", "\f"], ' ', $description))
                : null;

            $nameEs = collect($spec['names'] ?? [])
                ->firstWhere('language.name', 'es')['name']
                ?? $d['name'];

            $color = $spec['color']['name'] ?? null;
            $evolutionChainUrl = $spec['evolution_chain']['url'] ?? null;

            // Guardar o actualizar pokémon
            $pokemon = Pokemon::updateOrCreate(
                ['pokedex_id' => $d['id']],
                [
                    'generation'         => $generation,
                    'name'               => $d['name'],
                    'name_es'            => $nameEs,
                    'sprite'             => $d['sprites']['other']['official-artwork']['front_default']
                                           ?? $d['sprites']['front_default'] ?? null,
                    'sprite_animated'    => $d['sprites']['versions']['generation-v']['black-white']['animated']['front_default'] ?? null,
                    'hp'                 => $d['stats'][0]['base_stat'],
                    'attack'             => $d['stats'][1]['base_stat'],
                    'defense'            => $d['stats'][2]['base_stat'],
                    'sp_attack'          => $d['stats'][3]['base_stat'],
                    'sp_defense'         => $d['stats'][4]['base_stat'],
                    'speed'              => $d['stats'][5]['base_stat'],
                    'height'             => $d['height'],
                    'weight'             => $d['weight'],
                    'color'              => $color,
                    'description'        => $description,
                    'evolution_chain_url'=> $evolutionChainUrl,
                ]
            );

            // Tipos
            $pokemon->types()->delete();
            foreach ($d['types'] as $t) {
                PokemonType::create([
                    'pokemon_id' => $pokemon->id,
                    'type'       => $t['type']['name'],
                    'slot'       => $t['slot'],
                ]);
            }

            // Movimientos (máx 20, solo level-up)
            $pokemon->moves()->delete();
            $moves = collect($d['moves'])
                ->filter(fn($m) => collect($m['version_group_details'])
                    ->contains('move_learn_method.name', 'level-up'))
                ->take(20);

            foreach ($moves as $m) {
                $moveData = Http::withoutVerifying()
                    ->withHeaders(['User-Agent' => 'pokedex-laravel/1.0'])
                    ->timeout(8)->get($m['move']['url'])->json();

                PokemonMove::create([
                    'pokemon_id'   => $pokemon->id,
                    'move_name'    => $m['move']['name'],
                    'move_type'    => $moveData['type']['name'] ?? null,
                    'damage_class' => $moveData['damage_class']['name'] ?? null,
                    'power'        => $moveData['power'] ?? null,
                    'accuracy'     => $moveData['accuracy'] ?? null,
                    'learn_method' => 'level-up',
                ]);
            }

        } catch (\Exception $e) {
            $this->warn("Error en {$url}: " . $e->getMessage());
        }
    }
}