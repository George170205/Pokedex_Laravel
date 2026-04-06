@extends('layouts.app')

@push('styles')
<style>
    .about-wrap { max-width: 720px; margin: 0 auto; }

    .page-header { margin-bottom: 3rem; border-left: 3px solid var(--red); padding-left: 1.2rem; }
    .page-label { font-size: 0.65rem; color: var(--red); letter-spacing: 4px; text-transform: uppercase; margin-bottom: 0.5rem; display: block; }
    .page-title { font-family: 'Press Start 2P', monospace; font-size: 1.4rem; color: var(--text); line-height: 1.4; }

    .about-section {
        background: var(--bg2); border: 1px solid var(--border);
        padding: 2rem; margin-bottom: 1.5rem; position: relative;
    }

    .about-section::after {
        content: ''; position: absolute; top: 0; right: 0;
        border-top: 2px solid var(--red); border-right: 2px solid var(--red);
        width: 18px; height: 18px;
    }

    .section-title {
        font-family: 'Press Start 2P', monospace; font-size: 0.65rem;
        color: var(--red); letter-spacing: 3px; margin-bottom: 1rem; display: block;
    }

    .section-text { color: var(--text-dim); font-size: 0.85rem; line-height: 1.9; }

    .team-list { list-style: none; }
    .team-list li {
        display: flex; align-items: center; gap: 10px;
        padding: 0.6rem 0; border-bottom: 1px solid var(--border);
        font-size: 0.85rem; color: var(--text);
    }
    .team-list li:last-child { border-bottom: none; }
    .team-list li::before { content: '▸'; color: var(--red); }

    .tech-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 0.8rem; }
    .tech-item {
        background: var(--bg3); border: 1px solid var(--border);
        padding: 0.8rem 1rem; display: flex; align-items: center; gap: 8px;
        font-size: 0.8rem; color: var(--text);
    }
    .tech-item::before { content: '✓'; color: var(--yellow); font-size: 0.7rem; }
</style>
@endpush

@section('content')
<div class="about-wrap">
    <div class="page-header">
        <span class="page-label">// Acerca del Proyecto</span>
        <h1 class="page-title">About</h1>
    </div>

    <div class="about-section">
        <span class="section-title">// EQUIPO</span>
        <ul class="team-list">
            <li>Ruiz Rodriguez Jorge Alberto</li>
            <li>Abel Ramirez Dorame</li>
        </ul>
    </div>

    <div class="about-section">
        <span class="section-title">// OBJETIVO DEL PROYECTO</span>
        <p class="section-text">
            Pokédex Web es una aplicación desarrollada en Laravel como parte de la Unidad III
            de la materia de Herramientas para acelerar la construcción de software.
            El objetivo es aplicar el patrón MVC, consumir APIs externas y construir
            interfaces funcionales con diseño original.
        </p>
    </div>

    <div class="about-section">
        <span class="section-title">// TECNOLOGÍAS</span>
        <div class="tech-grid">
            <div class="tech-item">Laravel 13</div>
            <div class="tech-item">PHP 8.3</div>
            <div class="tech-item">PokéAPI v2</div>
            <div class="tech-item">Blade Templates</div>
            <div class="tech-item">Laravel Debugbar</div>
            <div class="tech-item">Laravel Pint</div>
            <div class="tech-item">CSS Grid</div>
            <div class="tech-item">Google Fonts</div>
        </div>
    </div>
</div>
@endsection
