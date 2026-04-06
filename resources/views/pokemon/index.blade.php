@extends('layouts.app')

@push('styles')
<style>
    .page-header {
        margin-bottom: 3rem;
        border-left: 3px solid var(--red);
        padding-left: 1.2rem;
    }

    .page-label {
        font-size: 0.65rem;
        color: var(--red);
        letter-spacing: 4px;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: block;
    }

    .page-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 1.6rem;
        color: var(--text);
        line-height: 1.4;
    }

    .pokemon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.2rem;
    }

    .pokemon-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.8rem 1rem;
        position: relative;
        overflow: hidden;
        transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
        opacity: 0;
        animation: cardIn 0.4s ease forwards;
    }

    .pokemon-card:nth-child(1)  { animation-delay: 0.05s; }
    .pokemon-card:nth-child(2)  { animation-delay: 0.10s; }
    .pokemon-card:nth-child(3)  { animation-delay: 0.15s; }
    .pokemon-card:nth-child(4)  { animation-delay: 0.20s; }
    .pokemon-card:nth-child(5)  { animation-delay: 0.25s; }
    .pokemon-card:nth-child(6)  { animation-delay: 0.30s; }
    .pokemon-card:nth-child(7)  { animation-delay: 0.35s; }
    .pokemon-card:nth-child(8)  { animation-delay: 0.40s; }
    .pokemon-card:nth-child(9)  { animation-delay: 0.45s; }
    .pokemon-card:nth-child(10) { animation-delay: 0.50s; }
    .pokemon-card:nth-child(11) { animation-delay: 0.55s; }
    .pokemon-card:nth-child(12) { animation-delay: 0.60s; }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pokemon-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,23,68,0.05) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.25s;
    }

    .pokemon-card:hover {
        border-color: var(--red);
        transform: translateY(-4px);
        box-shadow: 0 8px 32px var(--glow);
    }

    .pokemon-card:hover::before { opacity: 1; }

    .card-number {
        font-family: 'Press Start 2P', monospace;
        font-size: 0.55rem;
        color: var(--text-dim);
        margin-bottom: 1rem;
        letter-spacing: 2px;
    }

    .card-sprite {
        width: 80px;
        height: 80px;
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 2rem;
        transition: box-shadow 0.25s;
        position: relative;
    }

    .pokemon-card:hover .card-sprite {
        box-shadow: 0 0 16px var(--glow);
    }

    .card-name {
        font-family: 'Press Start 2P', monospace;
        font-size: 0.6rem;
        color: var(--text);
        text-align: center;
        letter-spacing: 1px;
        transition: color 0.2s;
    }

    .pokemon-card:hover .card-name { color: var(--red); }

    .card-arrow {
        position: absolute;
        bottom: 10px;
        right: 12px;
        font-size: 0.65rem;
        color: var(--text-dim);
        opacity: 0;
        transition: opacity 0.2s, transform 0.2s;
    }

    .pokemon-card:hover .card-arrow {
        opacity: 1;
        transform: translateX(3px);
    }

    .count-badge {
        display: inline-block;
        background: var(--bg3);
        border: 1px solid var(--border);
        color: var(--text-dim);
        font-size: 0.7rem;
        padding: 3px 10px;
        margin-top: 0.4rem;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <span class="page-label">// Registro Nacional</span>
    <h1 class="page-title">Pokémon</h1>
    <span class="count-badge">{{ count($pokemon) }} registros</span>
</div>

<div class="pokemon-grid">
    @foreach($pokemon as $index => $name)
    <a href="/pokemon/{{ $name }}" class="pokemon-card">
        <span class="card-number">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
        <div class="card-sprite">
            {{ mb_substr($name, 0, 1) }}
        </div>
        <span class="card-name">{{ strtoupper($name) }}</span>
        <span class="card-arrow">→</span>
    </a>
    @endforeach
</div>
@endsection
