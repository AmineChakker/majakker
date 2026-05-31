<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{{ $title ? $title.' — ' : '' }}Majakker Admin</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/utilities.css') }}"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
/* ── Reset ── */
html,body{height:100%;margin:0;overflow:hidden}

/* ── Admin brand palette ── */
.adm-root{
  --mj-purple:#7E5BEF;--mj-purple-deep:#5B3FE6;--mj-blue:#2563EB;
  --mj-cyan:#06B6D4;--mj-pink:#EC4899;--mj-emerald:#10B981;
  --mj-ink:#14152B;--mj-ink-2:#3F4360;--mj-ink-3:#7A7E96;--mj-ink-4:#B7BAC9;
  --mj-bg:#FAFAFC;--mj-surface:#FFFFFF;
  --mj-line:rgba(20,21,43,.08);--mj-line-2:rgba(20,21,43,.14);
  --mj-gradient:linear-gradient(135deg,#7E5BEF 0%,#2563EB 100%);
  --mj-gradient-soft:linear-gradient(135deg,rgba(126,91,239,.10) 0%,rgba(37,99,235,.10) 100%);
  /* override global tokens */
  --bg:var(--mj-bg);--surface:var(--mj-surface);
  --surface-2:#F2F2F8;--surface-3:#E6E7F0;
  --ink:var(--mj-ink);--ink-2:var(--mj-ink-2);--ink-3:var(--mj-ink-3);--ink-4:#B7BAC9;
  --line:var(--mj-line);--line-2:var(--mj-line-2);
  height:100%;background:var(--mj-bg);color:var(--mj-ink);font-family:var(--f-ui);
}

/* ── Animations ── */
@keyframes adm-fadeup{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
@keyframes adm-pulse{0%,100%{transform:scale(1);opacity:.95}50%{transform:scale(1.5);opacity:.3}}

/* ── Grain overlay ── */
.adm-grain{position:fixed;inset:0;z-index:200;pointer-events:none;opacity:.035;mix-blend-mode:multiply;
  background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.92' numOctaves='2' stitchTiles='stitch'/></filter><rect width='180' height='180' filter='url(%23n)'/></svg>")}

/* ── Shell ── */
.adm-shell{display:grid;grid-template-columns:252px 1fr;height:100%;background:var(--mj-bg)}

/* ── Sidebar ── */
.adm-side{position:relative;background:var(--mj-surface);border-right:0.5px solid var(--mj-line);
  padding:22px 14px 16px;display:flex;flex-direction:column;gap:18px;overflow:hidden}
.adm-side::before{content:'';position:absolute;top:0;right:0;width:1px;height:280px;
  background:linear-gradient(180deg,rgba(126,91,239,.5),rgba(37,99,235,.5),transparent);opacity:.4;pointer-events:none}
.adm-side-logo{display:inline-flex;padding:0 8px 4px}
.adm-side-logo img{height:36px;width:auto;display:block}
.adm-school-card{position:relative;overflow:hidden;padding:12px;border-radius:12px;
  background:linear-gradient(135deg,rgba(126,91,239,.08),rgba(37,99,235,.08));
  border:0.5px solid rgba(126,91,239,.18);display:flex;align-items:center;gap:10px}
.adm-school-logo{width:36px;height:36px;border-radius:10px;background:var(--mj-gradient);color:#fff;
  display:flex;align-items:center;justify-content:center;font:600 14px/1 var(--f-ui);
  box-shadow:0 4px 12px -4px rgba(94,57,224,.45);flex-shrink:0}
.adm-side-nav{display:flex;flex-direction:column;gap:18px;flex:1;min-height:0;overflow:auto}
.adm-side-group-h{font:500 9.5px/1 var(--f-mono);letter-spacing:.18em;text-transform:uppercase;
  color:var(--mj-ink-3);padding:0 12px 8px}
.adm-nav-item{display:flex;align-items:center;gap:12px;height:36px;padding:0 12px;border-radius:10px;
  font:500 13px/1 var(--f-ui);color:var(--mj-ink-2);text-decoration:none;
  transition:background .15s,color .15s;position:relative}
.adm-nav-item:hover{background:var(--surface-2);color:var(--mj-ink)}
.adm-nav-item.active{background:var(--mj-gradient);color:#fff;box-shadow:0 6px 18px -6px rgba(94,57,224,.45)}
.adm-nav-item.active .adm-nav-count{background:rgba(255,255,255,.22);color:#fff}
.adm-nav-icon{width:18px;height:18px;display:flex;align-items:center;justify-content:center;opacity:.85;flex-shrink:0}
.adm-nav-count{margin-left:auto;min-width:22px;height:20px;padding:0 7px;border-radius:999px;
  background:var(--surface-2);color:var(--mj-ink-3);font:500 10px/20px var(--f-mono);text-align:center}
.adm-side-foot{margin-top:auto;padding:10px;border-radius:12px;background:var(--surface-2);display:flex;align-items:center;gap:10px}

/* ── Topbar ── */
.adm-top{display:flex;align-items:center;gap:16px;padding:16px 28px;
  background:rgba(250,250,252,.85);backdrop-filter:blur(18px) saturate(180%);
  border-bottom:0.5px solid var(--mj-line);position:sticky;top:0;z-index:20;flex-shrink:0}
.adm-top-title{display:flex;flex-direction:column;gap:4px;min-width:0}
.adm-top-title .ttl{font:400 22px/1 var(--f-display);letter-spacing:-.012em}
.adm-top-title .sub{font:500 10.5px/1 var(--f-mono);letter-spacing:.16em;text-transform:uppercase;color:var(--mj-ink-3)}
.adm-search{display:flex;align-items:center;gap:10px;height:38px;padding:0 12px 0 14px;border-radius:10px;
  background:var(--mj-surface);border:0.5px solid var(--mj-line-2);width:320px;color:var(--mj-ink-3);font:400 13px/1 var(--f-ui)}
.adm-kbd{margin-left:auto;padding:2px 6px;border-radius:5px;background:var(--surface-2);
  color:var(--mj-ink-3);font:500 10px/1 var(--f-mono)}
.adm-live{display:inline-flex;align-items:center;gap:8px;padding:0 12px;height:28px;border-radius:999px;
  background:rgba(16,185,129,.10);color:#047857;font:500 10.5px/1 var(--f-mono);letter-spacing:.14em;text-transform:uppercase}
.adm-live-dot{width:6px;height:6px;border-radius:999px;background:#10B981;position:relative}
.adm-live-dot::after{content:'';position:absolute;inset:-3px;border-radius:999px;background:#10B981;
  animation:adm-pulse 1.8s ease-in-out infinite}
.adm-icon-btn{width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;
  background:var(--mj-surface);border:0.5px solid var(--mj-line-2);color:var(--mj-ink-2);
  cursor:pointer;transition:all .15s;position:relative;text-decoration:none}
.adm-icon-btn:hover{border-color:var(--mj-purple);color:var(--mj-purple);box-shadow:0 6px 16px -6px rgba(94,57,224,.22);transform:translateY(-1px)}
.adm-icon-badge{position:absolute;top:-3px;right:-3px;min-width:16px;height:16px;padding:0 4px;border-radius:999px;
  background:var(--mj-gradient);color:#fff;font:600 9px/16px var(--f-mono);text-align:center;border:1.5px solid var(--mj-bg)}
.adm-cta{display:inline-flex;align-items:center;gap:7px;height:38px;padding:0 16px;border-radius:10px;
  background:var(--mj-gradient);color:#fff;border:0;cursor:pointer;font:500 12.5px/1 var(--f-ui);
  box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 6px 18px -4px rgba(94,57,224,.45);transition:transform .2s,filter .2s;text-decoration:none}
.adm-cta:hover{transform:translateY(-1px);filter:brightness(1.08)}

/* ── Body ── */
.adm-body{height:100%;min-height:0;overflow:auto}
.adm-scroll::-webkit-scrollbar{width:10px;height:10px}
.adm-scroll::-webkit-scrollbar-thumb{background:rgba(20,21,43,.12);border-radius:999px;border:3px solid transparent;background-clip:content-box}

/* ── Animations ── */
.adm-fadeup{animation:adm-fadeup .8s cubic-bezier(.2,.7,.3,1) backwards}
.adm-d1{animation-delay:.05s}.adm-d2{animation-delay:.12s}.adm-d3{animation-delay:.20s}.adm-d4{animation-delay:.28s}

/* ── Eyebrow ── */
.adm-eyebrow{font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;
  color:var(--mj-ink-3);display:inline-flex;align-items:center;gap:12px}
.adm-eyebrow::before{content:'';width:28px;height:0.5px;background:var(--mj-ink-3)}

/* ── Gradient text ── */
.mj-grad-text{background:var(--mj-gradient);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic}
.mj-grad-num{background:var(--mj-gradient);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}

/* ── Card ── */
.adm-card{position:relative;overflow:hidden;background:var(--mj-surface);border:0.5px solid var(--mj-line);
  border-radius:16px;padding:22px;transition:border-color .25s,box-shadow .25s}
.adm-card:hover{border-color:var(--mj-line-2)}

/* ── KPI ── */
.adm-kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.adm-kpi{position:relative;overflow:hidden;background:var(--mj-surface);border:0.5px solid var(--mj-line);
  border-radius:16px;padding:18px;transition:transform .3s,box-shadow .3s,border-color .3s}
.adm-kpi::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;
  background:var(--kpi-accent,var(--mj-gradient));opacity:0;transform:scaleY(0);transform-origin:top;transition:opacity .3s,transform .35s}
.adm-kpi:hover{transform:translateY(-3px);box-shadow:0 18px 40px -16px rgba(20,21,43,.18);border-color:var(--mj-line-2)}
.adm-kpi:hover::before{opacity:1;transform:scaleY(1)}
.adm-kpi-h{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.adm-kpi-icon{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;
  background:var(--kpi-icon-bg);color:var(--kpi-icon-fg)}
.adm-kpi-label{font:500 11px/1 var(--f-mono);letter-spacing:.14em;text-transform:uppercase;color:var(--mj-ink-3)}
.adm-kpi-big{font:400 34px/1 var(--f-display);letter-spacing:-.02em;color:var(--mj-ink)}
.adm-kpi-prefix{font:500 12px/1 var(--f-mono);color:var(--mj-ink-3);margin-right:2px}
.adm-kpi-suffix{font:500 16px/1 var(--f-mono);color:var(--mj-ink-3);margin-left:2px}
.adm-kpi-bottom{display:flex;align-items:flex-end;justify-content:space-between;gap:10px;margin-top:14px}
.adm-kpi-delta{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;font:600 10.5px/1 var(--f-mono)}
.adm-kpi-delta.up{background:rgba(16,185,129,.10);color:#047857}
.adm-kpi-delta.down{background:rgba(220,38,38,.10);color:#B91C1C}
.adm-kpi-sub{font:400 11px/1.3 var(--f-ui);color:var(--mj-ink-3);margin-top:4px}
.adm-kpi-spark{display:flex;align-items:flex-end;gap:2px;height:30px;width:90px}
.adm-kpi-spark span{flex:1;min-width:2px;background:var(--surface-3);border-radius:1.5px}
.adm-kpi:hover .adm-kpi-spark span:last-child{background:var(--kpi-icon-fg)}

/* ── Chart ── */
.adm-chart-wrap{position:relative;height:240px;margin-top:14px}
.adm-chart-y{position:absolute;left:0;top:0;bottom:22px;width:28px;display:flex;flex-direction:column;justify-content:space-between}
.adm-chart-y span{font:500 9.5px/1 var(--f-mono);color:var(--mj-ink-4);letter-spacing:.06em}
.adm-chart-svg{position:absolute;left:32px;right:0;top:0;bottom:22px}
.adm-chart-x{position:absolute;left:32px;right:0;bottom:0;display:flex;justify-content:space-between}
.adm-chart-x span{font:500 9.5px/1 var(--f-mono);color:var(--mj-ink-4);letter-spacing:.06em}
.adm-tabs{display:inline-flex;padding:3px;background:var(--surface-2);border-radius:9px;border:0.5px solid var(--mj-line)}
.adm-tab{height:26px;padding:0 12px;font:500 11px/1 var(--f-ui);color:var(--mj-ink-3);border-radius:6px;border:0;background:transparent;cursor:pointer;transition:all .18s}
.adm-tab.active{background:var(--mj-surface);color:var(--mj-ink);box-shadow:0 1px 0 rgba(255,255,255,.6) inset,0 1px 2px rgba(20,21,43,.06)}

/* ── Moderation ── */
.adm-mod-item{display:flex;flex-direction:column;gap:8px;padding:12px;border-radius:12px;background:var(--surface-2);border:0.5px solid var(--mj-line)}
.adm-sev{display:inline-flex;align-items:center;gap:5px;height:18px;padding:0 8px;border-radius:999px;
  font:600 9.5px/1 var(--f-mono);letter-spacing:.08em;text-transform:uppercase}
.adm-sev::before{content:'';width:5px;height:5px;border-radius:999px;background:currentColor}
.adm-sev.high{background:rgba(236,72,153,.10);color:#BE185D}
.adm-sev.med{background:rgba(245,158,11,.12);color:#B45309}
.adm-sev.low{background:rgba(16,185,129,.10);color:#047857}

/* ── Schools list ── */
.adm-srow{display:grid;grid-template-columns:28px 1fr 60px;gap:12px;align-items:center;
  padding:12px 4px;border-top:0.5px solid var(--mj-line);transition:background .2s;border-radius:8px}
.adm-srow:first-of-type{border-top:0}
.adm-srow:hover{background:var(--surface-2)}
.adm-srow-rank{width:24px;height:24px;display:flex;align-items:center;justify-content:center;
  font:500 11px/1 var(--f-mono);color:var(--mj-ink-3);background:var(--surface-2);border-radius:7px}
.adm-srow:first-of-type .adm-srow-rank{background:var(--mj-gradient);color:#fff}
.adm-bar{width:100%;height:4px;border-radius:2px;background:var(--surface-3);overflow:hidden;margin-top:4px}
.adm-bar i{display:block;height:100%;background:var(--mj-gradient);border-radius:2px}

/* ── Revenue ── */
.adm-donut{position:relative;width:168px;height:168px;margin:0 auto}
.adm-donut-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.adm-rev-key{display:flex;align-items:center;gap:10px;padding:8px 0;border-top:0.5px solid var(--mj-line)}
.adm-rev-key:first-of-type{border-top:0}
.adm-rev-key .dot{width:8px;height:8px;border-radius:3px;flex-shrink:0}

/* ── Signups ── */
.adm-signup{display:flex;align-items:center;gap:10px;padding:10px 0;border-top:0.5px solid var(--mj-line)}
.adm-signup:first-of-type{border-top:0}
.adm-signup-flag{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;
  background:var(--mj-gradient-soft);color:var(--mj-purple);font:600 11px/1 var(--f-mono);flex-shrink:0}
.adm-tier{display:inline-flex;align-items:center;height:18px;padding:0 7px;border-radius:999px;
  font:600 9.5px/1 var(--f-mono);letter-spacing:.08em;text-transform:uppercase}
.adm-tier.pro{background:var(--mj-gradient);color:#fff}
.adm-tier.starter{background:rgba(20,21,43,.06);color:var(--mj-ink-2)}
.adm-tier.school{background:rgba(6,182,212,.10);color:#0E7490}

@media(max-width:1100px){.adm-kpi-row{grid-template-columns:repeat(2,1fr)}}
@media(max-width:760px){.adm-shell{grid-template-columns:1fr}.adm-side{display:none}}
</style>
</head>
<body>
<div class="adm-root">
<div class="adm-grain"></div>
<div class="adm-shell">

  {{-- ── Sidebar ── --}}
  @php
    $user = auth()->user();
    $currentRoute = request()->route()->getName() ?? '';
    $navGroups = [
      ['hd' => 'Plateforme', 'items' => [
        ['route' => 'admin.dashboard',  'label' => 'Tableau de bord', 'icon' => 'grid'],
        ['route' => 'admin.schools',    'label' => 'Écoles',          'icon' => 'classes', 'count' => \App\Models\School::count()],
        ['route' => 'admin.users',      'label' => 'Utilisateurs',    'icon' => 'users'],
        ['route' => 'admin.revenue',    'label' => 'Revenus',         'icon' => 'chart'],
      ]],
      ['hd' => 'Système', 'items' => [
        ['route' => 'admin.moderation', 'label' => 'Modération IA',   'icon' => 'moderation', 'count' => \App\Models\ModerationReport::where('status','pending')->count()],
        ['route' => 'admin.reports',    'label' => 'Rapports',        'icon' => 'flag'],
      ]],
    ];
  @endphp
  <aside class="adm-side">
    <a href="{{ route('admin.dashboard') }}" class="adm-side-logo">
      <img src="{{ asset('images/logo.png') }}" alt="Majakker"/>
    </a>

    <div class="adm-school-card">
      <div class="adm-school-logo">M</div>
      <div style="flex:1;min-width:0">
        <div style="font:500 11.5px/1.2 var(--f-ui)">EduSphere SA</div>
        <div style="font:400 10px/1.2 var(--f-ui);color:var(--mj-ink-3);margin-top:2px">Plateforme · {{ \App\Models\School::count() }} écoles</div>
      </div>
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--mj-ink-3)" stroke-width="1.4" stroke-linecap="round"><path d="M5 7.5L10 12.5L15 7.5"/></svg>
    </div>

    <nav class="adm-side-nav adm-scroll">
      @foreach($navGroups as $group)
      <div style="display:flex;flex-direction:column;gap:2px">
        <div class="adm-side-group-h">{{ $group['hd'] }}</div>
        @foreach($group['items'] as $item)
        @php $isActive = str_starts_with($currentRoute, $item['route']); @endphp
        <a href="{{ route($item['route']) }}" class="adm-nav-item {{ $isActive ? 'active' : '' }}">
          <span class="adm-nav-icon"><x-ui.icon name="{{ $item['icon'] }}" size="16"/></span>
          <span>{{ $item['label'] }}</span>
          @if(isset($item['count']) && $item['count'])
          <span class="adm-nav-count">{{ $item['count'] }}</span>
          @endif
        </a>
        @endforeach
      </div>
      @endforeach
    </nav>

    <div x-data="{ open: false }" style="position:relative">
      <div @click="open = !open" class="adm-side-foot" style="cursor:pointer" :style="open ? 'background:var(--surface-3)' : ''">
        <x-ui.avatar :name="$user->name" size="30"/>
        <div style="flex:1;min-width:0">
          <div style="font:500 11.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $user->short_name }}</div>
          <div style="font:400 10px/1.2 var(--f-ui);color:var(--mj-ink-3);margin-top:2px">Super admin</div>
        </div>
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--mj-ink-3)" stroke-width="1.5" stroke-linecap="round"
             :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s;flex-shrink:0">
          <path d="M5 8l5 5 5-5"/>
        </svg>
      </div>

      <div x-show="open" x-cloak @click.outside="open = false"
           x-transition:enter="transition ease-out duration-100"
           x-transition:enter-start="opacity-0 translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           style="position:absolute;bottom:calc(100% + 6px);left:0;right:0;background:var(--mj-surface);border:0.5px solid var(--mj-line-2);border-radius:10px;box-shadow:0 16px 40px -12px rgba(20,21,43,.2);overflow:hidden;z-index:50">
        <a href="{{ route('profile.edit') }}"
           style="display:flex;align-items:center;gap:9px;padding:9px 12px;font:400 12.5px/1 var(--f-ui);color:var(--mj-ink-2);text-decoration:none;transition:background .12s"
           onmouseenter="this.style.background='var(--surface-2)'" onmouseleave="this.style.background=''">
          <x-ui.icon name="settings" size="13" style="color:var(--mj-ink-3)"/>
          Paramètres
        </a>
        <div style="height:0.5px;background:var(--mj-line);margin:2px 0"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  style="display:flex;align-items:center;gap:9px;width:100%;padding:9px 12px;font:400 12.5px/1 var(--f-ui);color:#B91C1C;background:0;border:0;cursor:pointer;text-align:left;transition:background .12s"
                  onmouseenter="this.style.background='rgba(220,38,38,.05)'" onmouseleave="this.style.background=''">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M13 15l4-5-4-5M17 10H7M7 3H4a1 1 0 00-1 1v12a1 1 0 001 1h3"/>
            </svg>
            Se déconnecter
          </button>
        </form>
      </div>
    </div>
  </aside>

  {{-- ── Main ── --}}
  <div style="display:flex;flex-direction:column;min-width:0;min-height:0">

    {{-- Topbar --}}
    <header class="adm-top">
      <div class="adm-top-title">
        <span class="ttl">{{ $title }}</span>
        @if($subtitle)
        <span class="sub">{{ $subtitle }}</span>
        @endif
      </div>
      <span style="flex:1"></span>

      {{-- Search --}}
      <div class="adm-search" x-data="{open:false}" @click="open=true" style="cursor:default">
        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8.5" cy="8.5" r="5.5"/><path d="m13 13 4 4"/></svg>
        <span>Rechercher école, élève, transaction…</span>
        <span class="adm-kbd">⌘K</span>
      </div>

      {{-- Live pill --}}
      <div class="adm-live">
        <span class="adm-live-dot"></span>
        Production · Live
      </div>

      {{-- Notifications --}}
      <a href="{{ route('notifications.index') }}" class="adm-icon-btn">
        <x-ui.icon name="bell" size="15"/>
        @php $unread = auth()->user()->unreadNotifications->count(); @endphp
        @if($unread > 0)
        <span class="adm-icon-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
        @endif
      </a>

      {{-- CTA --}}
      <a href="{{ route('admin.schools') }}" class="adm-cta">
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
        Nouvelle école
      </a>
    </header>

    {{-- Page content --}}
    <div class="adm-body adm-scroll">
      {{ $slot }}
    </div>
  </div>

</div>
</div>
</body>
</html>
