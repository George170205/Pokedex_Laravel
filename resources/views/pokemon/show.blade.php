@extends('layouts.app')

@push('styles')
<style>
    .detail-wrap {
        max-width: 600px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-dim);
        text-decoration: none;
        font-size: 0.75rem;
        letter-spacing: 1px;
        margin-bottom: 2.5rem;
        transition: color 0.2s;
    }

    .back-link:hover { color: var(--red); }
    .back-link::before { content: '←'; }

    .detail-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Corner accent */
    .detail-card::before {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 80px;
        background: linear-gradient(225deg, rgba(255,23,68,0.15) 0%, transparent 60%);
    }

    .detail-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0;
        border-top: 2px solid var(--red);
        border-right: 2px solid var(--red);
        width: 24px; height: 24px;
    }

    .detail-label {
        font-size: 0.65rem;
        color: var(--red);
        letter-spacing: 4px;
        text-transform: uppercase;
        display: block;
        margin-bottom: 0.5rem;
    }

    .detail-name {
        font-family: 'Press Start 2P', monospace;
        font-size: clamp(1.2rem, 4vw, 2rem);
        color: var(--text);
        line-height: 1.4;
        margin-bottom: 2rem;
        word-break: break-word;
    }

    .sprite-container {
        width: 180px;
        height: 180px;
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2.5rem;
        font-size: 5rem;
        position: relative;
        box-shadow: 0 0 40px var(--glow), inset 0 0 30px rgba(255,23,68,0.05);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 30px var(--glow), inset 0 0 20px rgba(255,23,68,0.05); }
        50% { box-shadow: 0 0 60px rgba(255,23,68,0.5), inset 0 0 40px rgba(255,23,68,0.08); }
    }

    .detail-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .meta-item {
        background: var(--bg3);
        border: 1px solid var(--border);
        padding: 0.8rem 1rem;
    }

    .meta-key {
        font-size: 0.6rem;
        color: var(--text-dim);
        letter-spacing: 2px;
        display: block;
        margin-bottom: 4px;
    }

    .meta-value {
        font-family: 'Press Start 2P', monospace;
        font-size: 0.65rem;
        color: var(--yellow);
    }

    .detail-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .status-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2rem;
        font-size: 0.65rem;
        color: var(--text-dim);
        letter-spacing: 2px;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #00e676;
        box-shadow: 0 0 6px #00e676;
        animation: blink 2s infinite;
    }
</style>
@endpush

@section('content')
<div class="detail-wrap">
    <a href="/pokemon" class="back-link">Volver al listado</a>

    <div class="detail-card">
        <div class="status-bar">
            <span class="status-dot"></span>
            REGISTRO ACTIVO
        </div>

        <span class="detail-label">// Pokémon Detalle</span>
        <h1 class="detail-name">{{ $name }}</h1>

        <div class="sprite-container">
            {{ mb_substr($name, 0, 1) }}
        </div>

        <div class="detail-meta">
            <div class="meta-item">
                <span class="meta-key">NOMBRE</span>
                <span class="meta-value">{{ strtoupper($name) }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-key">ESTADO</span>
                <span class="meta-value">ACTIVO</span>
            </div>
            <div class="meta-item">
                <span class="meta-key">REGIÓN</span>
                <span class="meta-value">KANTO</span>
            </div>
            <div class="meta-item">
                <span class="meta-key">GENERACIÓN</span>
                <span class="meta-value">I</span>
            </div>
        </div>

        <div class="detail-actions">
            <a href="/pokemon" class="btn btn-outline">← Volver</a>
        </div>
    </div>
</div>
@endsection
