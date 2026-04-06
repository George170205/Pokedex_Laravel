@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 2rem; border-left: 3px solid var(--red); padding-left: 1.2rem; }
    .page-label { font-size: 0.65rem; color: var(--red); letter-spacing: 4px; text-transform: uppercase; margin-bottom: 0.5rem; display: block; }
    .page-title { font-family: 'Press Start 2P', monospace; font-size: 1.6rem; color: var(--text); line-height: 1.4; }

    /* Search form */
    .search-form {
        display: flex; gap: 0.8rem; margin-bottom: 2rem; flex-wrap: wrap;
    }

    .search-input {
        flex: 1; min-width: 220px;
        background: var(--bg2); border: 1px solid var(--border);
        color: var(--text); font-family: 'Share Tech Mono', monospace;
        font-size: 0.9rem; padding: 0.65rem 1rem;
        outline: none; transition: border-color 0.2s;
    }

    .search-input::placeholder { color: var(--text-dim); }
    .search-input:focus { border-color: var(--red); box-shadow: 0 0 10px var(--glow); }

    .search-btn {
        background: var(--red); color: #fff; border: none; cursor: pointer;
        font-family: 'Share Tech Mono', monospace; font-size: 0.85rem;
        padding: 0.65rem 1.4rem; letter-spacing: 1px;
        clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%);
        transition: background 0.2s, box-shadow 0.2s;
    }

    .search-btn:hover { background: #ff4569; box-shadow: 0 0 16px var(--glow); }

    .alert-error {
        background: rgba(255, 23, 68, 0.1); border: 1px solid var(--red);
        color: var(--red); padding: 0.75rem 1rem;
        font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 8px;
    }

    .alert-error::before { content: '⚠'; }

    /* Grid */
    .pokemon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.2rem; }

    .pokemon-card {
        background: var(--bg2); border: 1px solid var(--border);
        text-decoration: none; display: flex; flex-direction: column;
        align-items: center; padding: 1.5rem 1rem; position: relative;
        overflow: hidden; transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
        opacity: 0; animation: cardIn 0.4s ease forwards;
    }

    @for ($i = 1; $i <= 20; $i++)
    .pokemon-card:nth-child({{ $i }}) { animation-delay: {{ $i * 0.05 }}s; }
    @endfor

    @keyframes cardIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .pokemon-card::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,23,68,0.05) 0%, transparent 60%);
        opacity: 0; transition: opacity 0.25s;
    }

    .pokemon-card:hover { border-color: var(--red); transform: translateY(-4px); box-shadow: 0 8px 32px var(--glow); }
    .pokemon-card:hover::before { opacity: 1; }

    .card-number { font-family: 'Press Start 2P', monospace; font-size: 0.5rem; color: var(--text-dim); margin-bottom: 0.8rem; letter-spacing: 2px; }

    .card-sprite {
        width: 90px; height: 90px; margin-bottom: 0.8rem;
        image-rendering: pixelated;
    }

    .card-sprite-placeholder {
        width: 90px; height: 90px;
        background: var(--bg3); border: 1px solid var(--border); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; margin-bottom: 0.8rem;
    }

    .card-name {
        font-family: 'Press Start 2P', monospace; font-size: 0.55rem;
        color: var(--text); text-align: center; letter-spacing: 1px;
        text-transform: capitalize; transition: color 0.2s; margin-bottom: 0.8rem;
    }

    .pokemon-card:hover .card-name { color: var(--red); }

    .card-btn {
        background: transparent; border: 1px solid var(--border);
        color: var(--text-dim); font-family: 'Share Tech Mono', monospace;
        font-size: 0.7rem; padding: 4px 12px; text-decoration: none;
        transition: all 0.2s; letter-spacing: 1px;
    }

    .pokemon-card:hover .card-btn { border-color: var(--red); color: var(--red); }

    .count-badge {
        display: inline-block; background: var(--bg3);
        border: 1px solid var(--border); color: var(--text-dim);
        font-size: 0.7rem; padding: 3px 10px; margin-top: 0.4rem;
    }
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

{{-- Formulario de búsqueda --}}
<form action="/pokemon" method="POST" class="search-form">
    @csrf
    <input
        type="text"
        name="search"
        class="search-input"
        placeholder="Buscar Pokémon por nombre..."
        value="{{ $search ?? '' }}"
        autocomplete="off"
    >
    <button type="submit" class="search-btn">→ Buscar</button>
</form>

{{-- Error de validación o API --}}
@if($error)
    <div class="alert-error">{{ $error }}</div>
@endif

{{-- Grid de pokémon --}}
@if(count($pokemon) > 0)
<div class="pokemon-grid">
    @foreach($pokemon as $index => $poke)
    <a href="/pokemon/{{ $poke['name'] }}" class="pokemon-card">
        <span class="card-number">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
        <div class="card-sprite-placeholder">
            {{ mb_strtoupper(mb_substr($poke['name'], 0, 1)) }}
        </div>
        <span class="card-name">{{ $poke['name'] }}</span>
        <span class="card-btn">Ver →</span>
    </a>
    @endforeach
</div>
@elseif(!$error)
    <p style="color: var(--text-dim); font-size: 0.85rem;">No se encontraron resultados.</p>
@endif
@endsection
