@extends('layouts.app')

@push('styles')
<style>
    /* ── Header ── */
    .page-header { margin-bottom: 2rem; border-left: 3px solid var(--red); padding-left: 1.2rem; }
    .page-label  { font-size: 0.65rem; color: var(--red); letter-spacing: 4px; text-transform: uppercase; margin-bottom: 0.5rem; display: block; }
    .page-title  { font-family: 'Press Start 2P', monospace; font-size: 1.6rem; color: var(--text); line-height: 1.4; }
    .count-badge { display: inline-block; background: var(--bg3); border: 1px solid var(--border); color: var(--text-dim); font-size: 0.7rem; padding: 3px 10px; margin-top: 0.4rem; }

    /* ── Search ── */
    .search-form { display: flex; gap: 0.8rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .search-input { flex: 1; min-width: 220px; background: var(--bg2); border: 1px solid var(--border); color: var(--text); font-family: 'Share Tech Mono', monospace; font-size: 0.9rem; padding: 0.65rem 1rem; outline: none; transition: border-color 0.2s; }
    .search-input::placeholder { color: var(--text-dim); }
    .search-input:focus { border-color: var(--red); box-shadow: 0 0 10px var(--glow); }
    .search-btn { background: var(--red); color: #fff; border: none; cursor: pointer; font-family: 'Share Tech Mono', monospace; font-size: 0.85rem; padding: 0.65rem 1.4rem; letter-spacing: 1px; clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%); transition: background 0.2s, box-shadow 0.2s; }
    .search-btn:hover { background: #ff4569; box-shadow: 0 0 16px var(--glow); }

    /* ── Filtros ── */
    .filters-bar { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; padding: 1.2rem; background: var(--bg2); border: 1px solid var(--border); }
    .filters-label { font-size: 0.6rem; color: var(--red); letter-spacing: 4px; text-transform: uppercase; }

    .gen-filters, .type-filters { display: flex; flex-wrap: wrap; gap: 0.5rem; }

    .filter-btn {
        background: transparent; border: 1px solid var(--border); color: var(--text-dim);
        font-family: 'Share Tech Mono', monospace; font-size: 0.72rem; padding: 5px 14px;
        cursor: pointer; text-decoration: none; transition: all 0.2s; letter-spacing: 1px;
    }
    .filter-btn:hover  { border-color: var(--red); color: var(--red); }
    .filter-btn.active { background: var(--red); border-color: var(--red); color: #fff; }

    /* colores por tipo */
    .type-fire     { --tc: #F08030; } .type-water    { --tc: #6890F0; }
    .type-grass    { --tc: #78C850; } .type-electric { --tc: #F8D030; }
    .type-psychic  { --tc: #F85888; } .type-ice      { --tc: #98D8D8; }
    .type-dragon   { --tc: #7038F8; } .type-dark     { --tc: #705848; }
    .type-fairy    { --tc: #EE99AC; } .type-fighting { --tc: #C03028; }
    .type-poison   { --tc: #A040A0; } .type-ground   { --tc: #E0C068; }
    .type-flying   { --tc: #A890F0; } .type-bug      { --tc: #A8B820; }
    .type-rock     { --tc: #B8A038; } .type-ghost    { --tc: #705898; }
    .type-steel    { --tc: #B8B8D0; } .type-normal   { --tc: #A8A878; }

    .type-chip {
        font-size: 0.62rem; padding: 3px 10px; border: 1px solid var(--tc, var(--border));
        color: var(--tc, var(--text-dim)); background: transparent; text-transform: capitalize;
        letter-spacing: 1px;
    }
    .filter-btn.type-chip.active { background: var(--tc, var(--red)); border-color: var(--tc, var(--red)); color: #000; }

    /* ── Secciones por tipo ── */
    .type-section { margin-bottom: 3rem; }
    .type-section-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.2rem; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border); }
    .type-section-title { font-family: 'Press Start 2P', monospace; font-size: 0.75rem; color: var(--text); text-transform: capitalize; letter-spacing: 2px; }
    .type-count { font-size: 0.65rem; color: var(--text-dim); }

    /* ── Grid ── */
    .pokemon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; }

    .pokemon-card { background: var(--bg2); border: 1px solid var(--border); text-decoration: none; display: flex; flex-direction: column; align-items: center; padding: 1.2rem 1rem; position: relative; overflow: hidden; transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s; opacity: 0; animation: cardIn 0.4s ease forwards; }
    .pokemon-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,23,68,0.05) 0%, transparent 60%); opacity: 0; transition: opacity 0.25s; }
    .pokemon-card:hover { border-color: var(--red); transform: translateY(-4px); box-shadow: 0 8px 32px var(--glow); }
    .pokemon-card:hover::before { opacity: 1; }

    @keyframes cardIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .card-number { font-family: 'Press Start 2P', monospace; font-size: 0.5rem; color: var(--text-dim); margin-bottom: 0.6rem; letter-spacing: 2px; }
    .card-sprite { width: 80px; height: 80px; margin-bottom: 0.6rem; image-rendering: pixelated; }
    .card-sprite-placeholder { width: 80px; height: 80px; background: var(--bg3); border: 1px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 0.6rem; }
    .card-name { font-family: 'Press Start 2P', monospace; font-size: 0.5rem; color: var(--text); text-align: center; letter-spacing: 1px; text-transform: capitalize; transition: color 0.2s; margin-bottom: 0.5rem; }
    .pokemon-card:hover .card-name { color: var(--red); }
    .card-types { display: flex; gap: 4px; flex-wrap: wrap; justify-content: center; margin-bottom: 0.5rem; }
    .card-btn { background: transparent; border: 1px solid var(--border); color: var(--text-dim); font-family: 'Share Tech Mono', monospace; font-size: 0.65rem; padding: 3px 10px; text-decoration: none; transition: all 0.2s; letter-spacing: 1px; }
    .pokemon-card:hover .card-btn { border-color: var(--red); color: var(--red); }

    /* ── Alert ── */
    .alert-error { background: rgba(255,23,68,0.1); border: 1px solid var(--red); color: var(--red); padding: 0.75rem 1rem; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; }
    .alert-error::before { content: '⚠'; }

    .loading-msg { color: var(--text-dim); font-size: 0.85rem; text-align: center; padding: 3rem; }
</style>
@endpush

@section('content')

<div class="page-header">
    <span class="page-label">// Registro Nacional</span>
    <h1 class="page-title">Pokémon</h1>
    @if(count($pokemon) > 0)
        <span class="count-badge">{{ count($pokemon) }} registros</span>
    @endif
</div>

{{-- Búsqueda --}}
<form action="/pokemon" method="POST" class="search-form">
    @csrf
    <input type="text" name="search" class="search-input" placeholder="Buscar Pokémon por nombre..." value="{{ $search ?? '' }}" autocomplete="off">
    <button type="submit" class="search-btn">→ Buscar</button>
</form>

{{-- Filtros --}}
<div class="filters-bar">
    {{-- Por generación --}}
    <div>
        <span class="filters-label">// Generación</span>
        <div class="gen-filters" style="margin-top:0.5rem">
            @foreach([1 => 'Gen I', 2 => 'Gen II', 3 => 'Gen III'] as $g => $label)
                <a href="{{ route('pokemon.index', array_merge(request()->except('gen'), ['gen' => $g])) }}"
                   class="filter-btn {{ ($gen ?? 1) == $g ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Por tipo --}}
    @if(!empty($types))
    <div>
        <span class="filters-label">// Tipo</span>
        <div class="type-filters" style="margin-top:0.5rem">
            <a href="{{ route('pokemon.index', array_merge(request()->except('type'), ['gen' => $gen ?? 1])) }}"
               class="filter-btn {{ empty($filterType) ? 'active' : '' }}">
                Todos
            </a>
            @foreach($types as $type)
                <a href="{{ route('pokemon.index', array_merge(request()->except('type'), ['gen' => $gen ?? 1, 'type' => $type])) }}"
                   class="filter-btn type-chip type-{{ $type }} {{ ($filterType ?? '') === $type ? 'active' : '' }}">
                    {{ ucfirst($type) }}
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if($error)
    <div class="alert-error">{{ $error }}</div>
@endif

{{-- Secciones por tipo --}}
@if(!empty($byType))
    @foreach($byType as $typeName => $pokemons)
    <div class="type-section">
        <div class="type-section-header">
            <span class="type-chip type-{{ $typeName }}" style="font-family:'Share Tech Mono',monospace">{{ ucfirst($typeName) }}</span>
            <h2 class="type-section-title">{{ ucfirst($typeName) }}</h2>
            <span class="type-count">{{ count($pokemons) }} pokémon</span>
        </div>

        <div class="pokemon-grid">
            @foreach($pokemons as $index => $poke)
            <a href="/pokemon/{{ $poke['name'] }}" class="pokemon-card" style="animation-delay: {{ $index * 0.04 }}s">
                <span class="card-number">#{{ str_pad($poke['id'], 3, '0', STR_PAD_LEFT) }}</span>
                @if($poke['sprite'])
                    <img src="{{ $poke['sprite'] }}" alt="{{ $poke['name'] }}" class="card-sprite" loading="lazy">
                @else
                    <div class="card-sprite-placeholder">{{ mb_strtoupper(mb_substr($poke['name'], 0, 1)) }}</div>
                @endif
                <span class="card-name">{{ $poke['name'] }}</span>
                <div class="card-types">
                    @foreach($poke['types'] as $t)
                        <span class="type-chip type-{{ $t }}">{{ ucfirst($t) }}</span>
                    @endforeach
                </div>
                <span class="card-btn">Ver →</span>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach
@elseif(!$error)
    <p class="loading-msg">No se encontraron resultados.</p>
@endif

@endsection