<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#100d19">
    <title>@yield('title', 'Orbital | Mission Control')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <a class="brand" href="{{ route('tasks.index') }}" aria-label="Orbital Mission Control home">
            <span class="brand-mark" aria-hidden="true">O</span>
            <span class="brand-name">ORBITAL<small>PERSONAL TASKS</small></span>
        </a>

        <nav class="side-nav" aria-label="Mission navigation">
            <span class="side-label">Flight deck</span>
            <a class="nav-link {{ request()->routeIs('tasks.index') && request('status', 'all') === 'all' ? 'is-active' : '' }}" href="{{ route('tasks.index') }}">
                <span class="nav-glyph" aria-hidden="true">A</span>All missions
            </a>
            <a class="nav-link {{ request('status') === 'pending' ? 'is-active' : '' }}" href="{{ route('tasks.index', ['status' => 'pending']) }}">
                <span class="nav-glyph" aria-hidden="true">P</span>In orbit
            </a>
            <a class="nav-link {{ request('status') === 'completed' ? 'is-active' : '' }}" href="{{ route('tasks.index', ['status' => 'completed']) }}">
                <span class="nav-glyph" aria-hidden="true">C</span>Landed
            </a>
        </nav>

        <div class="side-spacer"></div>
        <div class="crew-status">
            <span class="side-label">Crew link</span>
            <div class="crew-row"><span class="signal-dot" aria-hidden="true"></span>Solo explorer</div>
            <p class="side-caption">Signal clear / systems ready</p>
        </div>
    </aside>

    <main class="workspace">
        <header class="topbar">
            <div class="topbar-path">ORBITAL <span aria-hidden="true">/</span> <strong>@yield('breadcrumb', 'MISSION CONTROL')</strong></div>
            <time class="topbar-date" datetime="{{ now()->toDateString() }}">{{ now()->format('D, d M Y') }}</time>
        </header>

        <div class="content">
            @if (session('success'))
                <div class="flash-message" role="status">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
</body>
</html>