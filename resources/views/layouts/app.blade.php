<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Pokédex') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: #FF1744;
            --yellow: #FFD600;
            --bg: #07070f;
            --bg2: #0e0e1a;
            --bg3: #16162a;
            --text: #e0e0f0;
            --text-dim: #8888aa;
            --border: #1e1e3a;
            --glow: rgba(255, 23, 68, 0.35);
        }

        html, body { min-height: 100vh; background: var(--bg); color: var(--text); font-family: 'Share Tech Mono', monospace; }

        body::before {
            content: '';
            position: fixed; inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.08) 2px, rgba(0,0,0,0.08) 4px);
            pointer-events: none; z-index: 9999;
        }

        nav {
            background: var(--bg2);
            border-bottom: 2px solid var(--red);
            padding: 0 2rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 64px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 24px var(--glow);
        }

        .nav-brand {
            font-family: 'Press Start 2P', monospace;
            font-size: 0.9rem; color: var(--red); text-decoration: none;
            letter-spacing: 2px; text-shadow: 0 0 12px var(--red);
            display: flex; align-items: center; gap: 10px;
        }

        .nav-brand::before { content: '◉'; animation: blink 2s infinite; }

        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        .nav-links { display: flex; gap: 2rem; list-style: none; }

        .nav-links a {
            color: var(--text-dim); text-decoration: none;
            font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase;
            transition: color 0.2s, text-shadow 0.2s;
            padding: 4px 0; border-bottom: 2px solid transparent;
        }

        .nav-links a:hover {
            color: var(--yellow); text-shadow: 0 0 8px var(--yellow); border-bottom-color: var(--yellow);
        }

        main { max-width: 1100px; margin: 0 auto; padding: 3rem 2rem; animation: fadeIn 0.4s ease; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        .btn {
            display: inline-block; padding: 0.6rem 1.4rem;
            font-family: 'Share Tech Mono', monospace; font-size: 0.85rem;
            letter-spacing: 1px; text-decoration: none; cursor: pointer; border: none; transition: all 0.2s;
        }

        .btn-red {
            background: var(--red); color: #fff;
            clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%);
        }

        .btn-red:hover { background: #ff4569; box-shadow: 0 0 20px var(--glow); transform: translateY(-2px); }

        .btn-outline { background: transparent; color: var(--text-dim); border: 1px solid var(--border); }
        .btn-outline:hover { border-color: var(--red); color: var(--red); }

        footer { text-align: center; padding: 2rem; color: var(--text-dim); font-size: 0.7rem; border-top: 1px solid var(--border); letter-spacing: 2px; }
    </style>
    @stack('styles')
</head>
<body>
    <nav>
        <a href="/" class="nav-brand">Pokédex</a>
        <ul class="nav-links">
            <li><a href="/">Inicio</a></li>
            <li><a href="/pokemon">Pokémon</a></li>
            <li><a href="/about">Acerca de</a></li>
        </ul>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>&lt;/&gt; POKÉDEX v2.0 — LARAVEL 13</footer>
</body>
</html>
