@extends('layouts.app')

@push('styles')
<style>
    .detail-wrap { max-width: 680px; margin: 0 auto; }

    .back-link {
        display: inline-flex; align-items: center; gap: 8px;
        color: var(--text-dim); text-decoration: none; font-size: 0.75rem;
        letter-spacing: 1px; margin-bottom: 2.5rem; transition: color 0.2s;
    }
    .back-link:hover { color: var(--red); }
    .back-link::before { content: '←'; }

    /* Error state */
    .error-card {
        background: var(--bg2); border: 1px solid var(--red);
        padding: 3rem 2.5rem; text-align: center;
    }

    .error-icon { font-size: 3rem; margin-bottom: 1rem; display: block; }
    .error-title { font-family: 'Press Start 2P', monospace; font-size: 1rem; color: var(--red); margin-bottom: 1rem; }
    .error-msg { color: var(--text-dim); font-size: 0.85rem; margin-bottom: 2rem; }

    /* Detail card */
    .detail-card {
        background: var(--bg2); border: 1px solid var(--border);
        padding: 2.5rem; position: relative; overflow: hidden;
    }

    .detail-card::after {
        content: ''; position: absolute; top: 0; right: 0;
        border-top: 2px solid var(--red); border-right: 2px solid var(--red);
        width: 24px; height: 24px;
    }

    .detail-top { display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap; margin-bottom: 2rem; }

    .sprite-wrap {
        width: 180px; height: 180px; flex-shrink: 0;
        background: var(--bg3); border: 1px solid var(--border);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 40px var(--glow);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 30px var(--glow); }
        50% { box-shadow: 0 0 60px rgba(255,23,68,0.5); }
    }

    .sprite-wrap img { width: 150px; height: 150px; image-rendering: pixelated; }
    .sprite-placeholder { font-size: 4rem; }

    .detail-info { flex: 1; }

    .detail-label { font-size: 0.65rem; color: var(--red); letter-spacing: 4px; display: block; margin-bottom: 0.4rem; }

    .detail-name {
        font-family: 'Press Start 2P', monospace;
        font-size: clamp(1rem, 3vw, 1.6rem);
        color: var(--text); line-height: 1.4; margin-bottom: 1.2rem;
        text-transform: capitalize;
    }

    .types { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.2rem; }

    .type-badge {
        font-size: 0.65rem; letter-spacing: 2px; text-transform: uppercase;
        padding: 3px 12px; border: 1px solid;
    }

    .type-fire    { border-color: #F08030; color: #F08030; }
    .type-water   { border-color: #6890F0; color: #6890F0; }
    .type-grass   { border-color: #78C850; color: #78C850; }
    .type-electric{ border-color: #F8D030; color: #F8D030; }
    .type-psychic { border-color: #F85888; color: #F85888; }
    .type-ice     { border-color: #98D8D8; color: #98D8D8; }
    .type-dragon  { border-color: #7038F8; color: #7038F8; }
    .type-dark    { border-color: #705848; color: #705848; }
    .type-normal  { border-color: #A8A878; color: #A8A878; }
    .type-poison  { border-color: #A040A0; color: #A040A0; }
    .type-ground  { border-color: #E0C068; color: #E0C068; }
    .type-rock    { border-color: #B8A038; color: #B8A038; }
    .type-bug     { border-color: #A8B820; color: #A8B820; }
    .type-ghost   { border-color: #705898; color: #705898; }
    .type-steel   { border-color: #B8B8D0; color: #B8B8D0; }
    .type-fairy   { border-color: #EE99AC; color: #EE99AC; }
    .type-fighting{ border-color: #C03028; color: #C03028; }
    .type-flying  { border-color: #A890F0; color: #A890F0; }

    /* Stats */
    .stats-section { margin-bottom: 2rem; }
    .stats-title { font-family: 'Press Start 2P', monospace; font-size: 0.6rem; color: var(--text-dim); letter-spacing: 3px; margin-bottom: 1rem; display: block; }

    .stat-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; }
    .stat-name { font-size: 0.65rem; color: var(--text-dim); letter-spacing: 2px; width: 70px; flex-shrink: 0; }
    .stat-val { font-family: 'Press Start 2P', monospace; font-size: 0.6rem; color: var(--yellow); width: 36px; text-align: right; flex-shrink: 0; }
    .stat-bar { flex: 1; height: 6px; background: var(--bg3); border: 1px solid var(--border); }
    .stat-fill { height: 100%; background: var(--red); transition: width 1s ease; }

    /* Meta grid */
    .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 2rem; }
    .meta-item { background: var(--bg3); border: 1px solid var(--border); padding: 0.8rem 1rem; }
    .meta-key { font-size: 0.6rem; color: var(--text-dim); letter-spacing: 2px; display: block; margin-bottom: 4px; }
    .meta-value { font-family: 'Press Start 2P', monospace; font-size: 0.6rem; color: var(--yellow); }

    .status-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.65rem; color: var(--text-dim); letter-spacing: 2px; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #00e676; box-shadow: 0 0 6px #00e676; animation: blink 2s infinite; }
</style>
@endpush

@section('content')
<div class="detail-wrap">
    <a href="/pokemon" class="back-link">Volver al listado</a>

    @if($error)
    <div class="error-card">
        <span class="error-icon">✕</span>
        <h2 class="error-title">Pokémon no encontrado</h2>
        <p class="error-msg">{{ $error }}</p>
        <a href="/pokemon" class="btn btn-red">← Volver al listado</a>
    </div>

    @elseif($pokemon)
    <div class="detail-card">
        <div class="status-bar">
            <span class="status-dot"></span> REGISTRO ACTIVO
        </div>

        <div class="detail-top">
            <div class="sprite-wrap">
                @if($pokemon['sprite'])
                    <img src="{{ $pokemon['sprite'] }}" alt="{{ $pokemon['name'] }}">
                @else
                    <span class="sprite-placeholder">{{ mb_strtoupper(mb_substr($pokemon['name'], 0, 1)) }}</span>
                @endif
            </div>

            <div class="detail-info">
                <span class="detail-label">// Pokémon Detalle</span>
                <h1 class="detail-name">{{ $pokemon['name'] }}</h1>

                <div class="types">
                    @foreach($pokemon['types'] as $type)
                        <span class="type-badge type-{{ $type }}">{{ $type }}</span>
                    @endforeach
                </div>

                <div class="meta-grid">
                    <div class="meta-item">
                        <span class="meta-key">ALTURA</span>
                        <span class="meta-value">{{ $pokemon['height'] / 10 }}m</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-key">PESO</span>
                        <span class="meta-value">{{ $pokemon['weight'] / 10 }}kg</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-section">
            <span class="stats-title">// BASE STATS</span>

            <div class="stat-row">
                <span class="stat-name">HP</span>
                <span class="stat-val">{{ $pokemon['hp'] }}</span>
                <div class="stat-bar"><div class="stat-fill" style="width: {{ min($pokemon['hp'], 255) / 255 * 100 }}%"></div></div>
            </div>
            <div class="stat-row">
                <span class="stat-name">ATTACK</span>
                <span class="stat-val">{{ $pokemon['attack'] }}</span>
                <div class="stat-bar"><div class="stat-fill" style="width: {{ min($pokemon['attack'], 255) / 255 * 100 }}%"></div></div>
            </div>
            <div class="stat-row">
                <span class="stat-name">DEFENSE</span>
                <span class="stat-val">{{ $pokemon['defense'] }}</span>
                <div class="stat-bar"><div class="stat-fill" style="width: {{ min($pokemon['defense'], 255) / 255 * 100 }}%"></div></div>
            </div>
        </div>

        <a href="/pokemon" class="btn btn-outline">← Volver</a>
    </div>
    @endif
</div>
@endsection
