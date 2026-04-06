@extends('layouts.app')

@push('styles')
<style>
    .hero {
        min-height: 70vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        gap: 2rem;
    }

    /* Background grid */
    .hero::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,23,68,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,23,68,0.04) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
    }

    .hero-number {
        font-family: 'Press Start 2P', monospace;
        font-size: clamp(5rem, 18vw, 12rem);
        color: transparent;
        -webkit-text-stroke: 1px rgba(255,23,68,0.15);
        line-height: 1;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -55%);
        user-select: none;
        letter-spacing: -0.05em;
    }

    .hero-tag {
        font-size: 0.7rem;
        color: var(--red);
        letter-spacing: 6px;
        text-transform: uppercase;
        opacity: 0;
        animation: slideUp 0.5s ease 0.1s forwards;
    }

    .hero-title {
        font-family: 'Press Start 2P', monospace;
        font-size: clamp(1.2rem, 4vw, 2.2rem);
        line-height: 1.5;
        color: var(--text);
        text-shadow: 0 0 30px rgba(255,23,68,0.3);
        opacity: 0;
        animation: slideUp 0.5s ease 0.25s forwards;
        position: relative;
    }

    .hero-title span {
        color: var(--red);
    }

    .hero-desc {
        color: var(--text-dim);
        font-size: 0.9rem;
        max-width: 480px;
        line-height: 1.8;
        opacity: 0;
        animation: slideUp 0.5s ease 0.4s forwards;
    }

    .hero-cta {
        opacity: 0;
        animation: slideUp 0.5s ease 0.55s forwards;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .hero-stats {
        display: flex;
        gap: 3rem;
        margin-top: 1rem;
        opacity: 0;
        animation: slideUp 0.5s ease 0.7s forwards;
    }

    .stat {
        text-align: center;
    }

    .stat-value {
        font-family: 'Press Start 2P', monospace;
        font-size: 1.4rem;
        color: var(--red);
        display: block;
    }

    .stat-label {
        font-size: 0.65rem;
        color: var(--text-dim);
        letter-spacing: 2px;
        margin-top: 4px;
        display: block;
    }

    .divider {
        width: 1px;
        height: 40px;
        background: var(--border);
        align-self: center;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="hero">
    <div class="hero-number">001</div>

    <span class="hero-tag">// Sistema Pokédex Web</span>

    <h1 class="hero-title">
        Captura. Explora.<br><span>Descubre.</span>
    </h1>

    <p class="hero-desc">
        Tu Pokédex digital. Explora la lista completa de Pokémon,
        consulta sus datos y descubre todos sus detalles.
    </p>

    <div class="hero-cta">
        <a href="/pokemon" class="btn btn-red">→ Ver Pokémon</a>
    </div>

    <div class="hero-stats">
        <div class="stat">
            <span class="stat-value">12</span>
            <span class="stat-label">Pokémon</span>
        </div>
        <div class="divider"></div>
        <div class="stat">
            <span class="stat-value">3</span>
            <span class="stat-label">Vistas</span>
        </div>
        <div class="divider"></div>
        <div class="stat">
            <span class="stat-value">v13</span>
            <span class="stat-label">Laravel</span>
        </div>
    </div>
</div>
@endsection
