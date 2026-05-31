<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>{{ $title ?? 'EduSphere' }} — Majakker</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/utilities.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/animations.css') }}"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
/* ── Override warm palette with cool blue-tinted Majakker brand theme ── */
:root {
  --bg:        #FAFAFC;
  --surface:   #FFFFFF;
  --surface-2: #F2F2F8;
  --surface-3: #E6E7F0;
  --ink:       #14152B;
  --ink-2:     #3F4360;
  --ink-3:     #7A7E96;
  --ink-4:     #B7BAC9;
  --line:      rgba(20,21,43,.08);
  --line-2:    rgba(20,21,43,.14);
  --c-blue:       #2563EB;
  --c-blue-soft:  #E8EEFE;
  --c-saffron:    #7E5BEF;
  --c-saffron-soft: #EDE7FE;
  --c-terracotta: #EC4899;
  --c-terracotta-soft: #FCE7F3;
  --c-atlas:      #4A9B8E;
  --c-atlas-soft: #DCEFEC;
}

html,body{height:100%;margin:0;overflow:hidden;}
.app-shell{display:flex;height:100%;width:100%;background:var(--bg);}
.app-main{flex:1;display:flex;flex-direction:column;min-width:0;height:100%;}
.app-body{flex:1;min-height:0;overflow:hidden;}

/* ── Nav item — gradient active state ── */
.nav-item.active {
  background: linear-gradient(135deg,#7E5BEF,#2563EB) !important;
  color: #fff !important;
  box-shadow: 0 4px 14px -4px rgba(94,57,224,.4);
}
.nav-item.active svg, .nav-item.active x-ui\.icon {
  color: rgba(255,255,255,.9) !important;
}
.nav-item { border-radius: 10px; }
.nav-item:hover { background: var(--surface-2); }

/* ── Chip overrides for blue ink base ── */
.chip-saffron    { background: #EDE7FE; color: #6D28D9; }
.chip-terracotta { background: #FCE7F3; color: #BE185D; }
.chip-atlas      { background: #DCEFEC; color: #2D6B61; }
.chip-blue       { background: #E8EEFE; color: #1D4ED8; }

/* Mobile sidebar overlay */
.sidebar-overlay{display:none;position:fixed;inset:0;z-index:39;background:rgba(0,0,0,.35);backdrop-filter:blur(2px)}
.mob-menu-btn{display:none;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:0.5px solid var(--line);background:var(--surface);cursor:pointer;flex-shrink:0}

@media(max-width:767px){
  aside.app-sidebar{
    position:fixed!important;left:-260px;top:0;bottom:0;z-index:40;
    transition:transform .25s cubic-bezier(.4,0,.2,1);
    box-shadow:none;
  }
  aside.app-sidebar.open{
    transform:translateX(260px);
    box-shadow:var(--sh-lg);
  }
  .sidebar-overlay.open{display:block}
  .mob-menu-btn{display:flex}
  .app-shell{overflow:hidden}
}
</style>
</head>
<body style="background:var(--bg)" x-data="{ sidebarOpen: false }">

{{-- Mobile overlay --}}
<div class="sidebar-overlay" :class="{ open: sidebarOpen }" @click="sidebarOpen=false"></div>

<div class="app-shell">
    {{-- Sidebar --}}
    @include('components.sidebar')

    <div class="app-main">
        {{-- Topbar --}}
        @include('components.topbar', ['title' => $title ?? '', 'subtitle' => $subtitle ?? ''])

        {{-- Page body --}}
        <div class="app-body">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
