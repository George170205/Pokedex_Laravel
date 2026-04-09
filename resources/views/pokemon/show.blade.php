@extends('layouts.app')

@push('styles')
<style>
    .detail-wrap { max-width: 900px; margin: 0 auto; }

    .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--text-dim); text-decoration: none; font-size: 0.75rem; letter-spacing: 1px; margin-bottom: 2.5rem; transition: color 0.2s; }
    .back-link:hover { color: var(--red); }
    .back-link::before { content: '←'; }

    /* Error */
    .error-card { background: var(--bg2); border: 1px solid var(--red); padding: 3rem 2.5rem; text-align: center; }
    .error-icon { font-size: 3rem; margin-bottom: 1rem; display: block; }
    .error-title { font-family: 'Press Start 2P', monospace; font-size: 1rem; color: var(--red); margin-bottom: 1rem; }
    .error-msg { color: var(--text-dim); font-size: 0.85rem; margin-bottom: 2rem; }

    /* Hero */
    .detail-hero {
        background: var(--bg2); border: 1px solid var(--border);
        padding: 2.5rem; position: relative; overflow: hidden; margin-bottom: 1.5rem;
    }
    .detail-hero::after { content: ''; position: absolute; top: 0; right: 0; border-top: 2px solid var(--red); border-right: 2px solid var(--red); width: 24px; height: 24px; }

    .detail-top { display: flex; gap: 2.5rem; align-items: flex-start; flex-wrap: wrap; }

    .sprite-wrap {
        width: 200px; height: 200px; flex-shrink: 0;
        background: var(--bg3); border: 1px solid var(--border); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 40px var(--glow); animation: pulse 3s ease-in-out infinite;
        position: relative; cursor: pointer;
    }
    @keyframes pulse { 0%,100% { box-shadow: 0 0 30px var(--glow); } 50% { box-shadow: 0 0 60px rgba(255,23,68,0.5); } }
    .sprite-wrap img { width: 170px; height: 170px; image-rendering: pixelated; transition: opacity 0.3s; }
    .sprite-hint { position: absolute; bottom: 6px; font-size: 0.5rem; color: var(--text-dim); letter-spacing: 1px; }

    .detail-info { flex: 1; min-width: 220px; }
    .detail-number { font-family: 'Press Start 2P', monospace; font-size: 0.6rem; color: var(--red); letter-spacing: 4px; display: block; margin-bottom: 0.5rem; }
    .detail-name { font-family: 'Press Start 2P', monospace; font-size: clamp(1rem, 3vw, 1.8rem); color: var(--text); line-height: 1.4; margin-bottom: 0.3rem; text-transform: capitalize; }
    .detail-name-es { font-size: 0.8rem; color: var(--text-dim); margin-bottom: 1.2rem; display: block; text-transform: capitalize; }

    .status-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 1.2rem; font-size: 0.65rem; color: var(--text-dim); letter-spacing: 2px; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #00e676; box-shadow: 0 0 6px #00e676; animation: blink 2s infinite; }
    @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0.3; } }

    .types { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.2rem; }
    .type-badge { font-size: 0.65rem; letter-spacing: 2px; text-transform: uppercase; padding: 3px 12px; border: 1px solid; }

    /* Colores tipos */
    .type-fire{border-color:#F08030;color:#F08030}.type-water{border-color:#6890F0;color:#6890F0}.type-grass{border-color:#78C850;color:#78C850}.type-electric{border-color:#F8D030;color:#F8D030}.type-psychic{border-color:#F85888;color:#F85888}.type-ice{border-color:#98D8D8;color:#98D8D8}.type-dragon{border-color:#7038F8;color:#7038F8}.type-dark{border-color:#705848;color:#705848}.type-normal{border-color:#A8A878;color:#A8A878}.type-poison{border-color:#A040A0;color:#A040A0}.type-ground{border-color:#E0C068;color:#E0C068}.type-rock{border-color:#B8A038;color:#B8A038}.type-bug{border-color:#A8B820;color:#A8B820}.type-ghost{border-color:#705898;color:#705898}.type-steel{border-color:#B8B8D0;color:#B8B8D0}.type-fairy{border-color:#EE99AC;color:#EE99AC}.type-fighting{border-color:#C03028;color:#C03028}.type-flying{border-color:#A890F0;color:#A890F0}

    .meta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.6rem; margin-bottom: 1.2rem; }
    .meta-item { background: var(--bg3); border: 1px solid var(--border); padding: 0.7rem 0.8rem; }
    .meta-key { font-size: 0.55rem; color: var(--text-dim); letter-spacing: 2px; display: block; margin-bottom: 4px; }
    .meta-value { font-family: 'Press Start 2P', monospace; font-size: 0.6rem; color: var(--yellow, #F8D030); }

    .description-box { background: var(--bg3); border-left: 2px solid var(--red); padding: 1rem 1.2rem; font-size: 0.82rem; color: var(--text-dim); line-height: 1.8; font-style: italic; }

    /* Secciones */
    .section { background: var(--bg2); border: 1px solid var(--border); padding: 1.8rem; margin-bottom: 1.5rem; }
    .section-title { font-family: 'Press Start 2P', monospace; font-size: 0.6rem; color: var(--red); letter-spacing: 4px; text-transform: uppercase; margin-bottom: 1.5rem; display: block; padding-bottom: 0.8rem; border-bottom: 1px solid var(--border); }

    /* Stats */
    .stat-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.8rem; }
    .stat-name { font-size: 0.62rem; color: var(--text-dim); letter-spacing: 2px; width: 80px; flex-shrink: 0; }
    .stat-val { font-family: 'Press Start 2P', monospace; font-size: 0.6rem; color: var(--yellow, #F8D030); width: 36px; text-align: right; flex-shrink: 0; }
    .stat-bar { flex: 1; height: 6px; background: var(--bg3); border: 1px solid var(--border); }
    .stat-fill { height: 100%; transition: width 1.2s ease; }
    .stat-fill.hp      { background: #ff5959; }
    .stat-fill.atk     { background: #F08030; }
    .stat-fill.def     { background: #6890F0; }
    .stat-fill.spatk   { background: #F85888; }
    .stat-fill.spdef   { background: #78C850; }
    .stat-fill.spd     { background: #F8D030; }

    /* Evoluciones */
    .evo-chain { display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
    .evo-item { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; text-decoration: none; padding: 1rem; background: var(--bg3); border: 1px solid var(--border); transition: all 0.2s; min-width: 100px; }
    .evo-item:hover { border-color: var(--red); transform: translateY(-3px); }
    .evo-item.current { border-color: var(--red); background: rgba(255,23,68,0.05); }
    .evo-item img { width: 80px; height: 80px; image-rendering: pixelated; }
    .evo-item .evo-placeholder { width: 80px; height: 80px; background: var(--bg2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .evo-name { font-family: 'Press Start 2P', monospace; font-size: 0.45rem; color: var(--text); text-transform: capitalize; text-align: center; }
    .evo-name-es { font-size: 0.65rem; color: var(--text-dim); text-transform: capitalize; }
    .evo-arrow { font-size: 1.2rem; color: var(--red); align-self: center; }
    .evo-types { display: flex; gap: 4px; flex-wrap: wrap; justify-content: center; }
    .evo-type-chip { font-size: 0.5rem; padding: 2px 6px; border: 1px solid; text-transform: capitalize; }

    /* Debilidades */
    .weakness-grid { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .weakness-chip { font-size: 0.65rem; padding: 4px 12px; border: 1px solid; text-transform: capitalize; letter-spacing: 1px; }

    /* Movimientos */
    .moves-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .moves-table th { font-size: 0.55rem; color: var(--text-dim); letter-spacing: 3px; text-align: left; padding: 0.5rem 0.8rem; border-bottom: 1px solid var(--border); }
    .moves-table td { padding: 0.6rem 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.04); color: var(--text); text-transform: capitalize; }
    .moves-table tr:hover td { background: var(--bg3); }
    .move-type-chip { font-size: 0.55rem; padding: 2px 8px; border: 1px solid; text-transform: capitalize; }
    .damage-physical { color: #F08030; font-size: 0.6rem; }
    .damage-special  { color: #6890F0; font-size: 0.6rem; }
    .damage-status   { color: #78C850; font-size: 0.6rem; }
    .no-data { color: var(--text-dim); font-size: 0.75rem; }
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

    {{-- ── HERO ── --}}
    <div class="detail-hero">
        <div class="detail-top">

            {{-- Sprite --}}
            <div class="sprite-wrap" id="spriteWrap" title="Click para animar">
                @if($pokemon['sprite'])
                    <img id="spriteImg" src="{{ $pokemon['sprite'] }}" alt="{{ $pokemon['name'] }}">
                @else
                    <span style="font-size:4rem">{{ mb_strtoupper(mb_substr($pokemon['name'],0,1)) }}</span>
                @endif
                @if($pokemon['sprite_animated'] ?? null)
                    <span class="sprite-hint">CLICK</span>
                @endif
            </div>

            {{-- Info --}}
            <div class="detail-info">
                <div class="status-bar">
                    <span class="status-dot"></span> REGISTRO ACTIVO
                </div>

                <span class="detail-number">#{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}</span>
                <h1 class="detail-name">{{ $pokemon['name'] }}</h1>
                @if(($pokemon['name_es'] ?? '') && $pokemon['name_es'] !== $pokemon['name'])
                    <span class="detail-name-es">{{ $pokemon['name_es'] }}</span>
                @endif

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
                    <div class="meta-item">
                        <span class="meta-key">COLOR</span>
                        <span class="meta-value">{{ ucfirst($pokemon['color'] ?? '—') }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-key">GEN</span>
                        <span class="meta-value">{{ $pokemon['id'] <= 151 ? 'I' : ($pokemon['id'] <= 251 ? 'II' : 'III') }}</span>
                    </div>
                </div>

                @if($pokemon['description'] ?? null)
                <div class="description-box">
                    {{ $pokemon['description'] }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── STATS ── --}}
    <div class="section">
        <span class="section-title">// Base Stats</span>
        @foreach([
            ['HP',      'hp',     $pokemon['hp'],       'hp'],
            ['ATTACK',  'attack', $pokemon['attack'],   'atk'],
            ['DEFENSE', 'def',    $pokemon['defense'],  'def'],
            ['SP.ATK',  'spatk',  $pokemon['sp_attack'],'spatk'],
            ['SP.DEF',  'spdef',  $pokemon['sp_defense'],'spdef'],
            ['SPEED',   'spd',    $pokemon['speed'],    'spd'],
        ] as [$label, $key, $val, $cls])
        <div class="stat-row">
            <span class="stat-name">{{ $label }}</span>
            <span class="stat-val">{{ $val }}</span>
            <div class="stat-bar">
                <div class="stat-fill {{ $cls }}" style="width: {{ min($val, 255) / 255 * 100 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── EVOLUCIONES ── --}}
    @if(!empty($evolutions))
    <div class="section">
        <span class="section-title">// Cadena de Evolución</span>
        <div class="evo-chain">
            @foreach($evolutions as $i => $evo)
                @if($i > 0)
                    <span class="evo-arrow">→</span>
                @endif
                <a href="/pokemon/{{ $evo['name'] }}"
                   class="evo-item {{ $evo['name'] === $pokemon['name'] ? 'current' : '' }}">
                    @if($evo['sprite'])
                        <img src="{{ $evo['sprite'] }}" alt="{{ $evo['name'] }}">
                    @else
                        <div class="evo-placeholder">{{ mb_strtoupper(mb_substr($evo['name'],0,1)) }}</div>
                    @endif
                    <span class="evo-name-es">{{ ucfirst($evo['name_es'] ?? $evo['name']) }}</span>
                    <span class="evo-name">{{ $evo['name'] }}</span>
                    <div class="evo-types">
                        @foreach($evo['types'] as $et)
                            <span class="evo-type-chip type-{{ $et }}">{{ $et }}</span>
                        @endforeach
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── DEBILIDADES ── --}}
    @if(!empty($weaknesses))
    <div class="section">
        <span class="section-title">// Debilidades</span>
        <div class="weakness-grid">
            @foreach($weaknesses as $w)
                <span class="weakness-chip type-{{ $w }}">{{ ucfirst($w) }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── MOVIMIENTOS ── --}}
    <div class="section">
        <span class="section-title">// Movimientos (Level-Up)</span>
        @if(!empty($pokemon['moves']))
        <table class="moves-table">
            <thead>
                <tr>
                    <th>MOVIMIENTO</th>
                    <th>TIPO</th>
                    <th>CLASE</th>
                    <th>PODER</th>
                    <th>PRECISIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pokemon['moves'] as $move)
                <tr>
                    <td>{{ str_replace('-', ' ', $move['name']) }}</td>
                    <td>
                        @if($move['type'])
                            <span class="move-type-chip type-{{ $move['type'] }}">{{ $move['type'] }}</span>
                        @else
                            <span class="no-data">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="damage-{{ $move['damage_class'] ?? 'status' }}">
                            {{ ucfirst($move['damage_class'] ?? '—') }}
                        </span>
                    </td>
                    <td>{{ $move['power'] ?? '—' }}</td>
                    <td>{{ $move['accuracy'] ? $move['accuracy'].'%' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="no-data">No hay movimientos registrados.</p>
        @endif
    </div>

    @endif
</div>

{{-- Sprite animado toggle --}}
@if(($pokemon['sprite_animated'] ?? null) && ($pokemon['sprite'] ?? null))
<script>
    const wrap   = document.getElementById('spriteWrap');
    const img    = document.getElementById('spriteImg');
    const normal = "{{ $pokemon['sprite'] }}";
    const anim   = "{{ $pokemon['sprite_animated'] }}";
    let animated = false;
    wrap.addEventListener('click', () => {
        animated = !animated;
        img.src  = animated ? anim : normal;
    });
</script>
@endif
@endsection