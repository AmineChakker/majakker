@props(['title' => '', 'subtitle' => ''])
@php $unread = auth()->user()->unreadNotifications->count(); @endphp

<header style="display:flex;align-items:center;gap:14px;padding:12px 22px;background:rgba(250,247,242,0.85);backdrop-filter:blur(18px) saturate(160%);border-bottom:0.5px solid var(--line);position:sticky;top:0;z-index:10;flex-shrink:0;">
    {{-- Mobile menu button --}}
    <button class="mob-menu-btn" @click="sidebarOpen=!sidebarOpen" aria-label="Menu">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
            <path d="M3 5h14M3 10h14M3 15h14"/>
        </svg>
    </button>
    <div style="display:flex;flex-direction:column;gap:2px;min-width:0">
        <div style="display:flex;align-items:center;gap:8px">
            <span class="serif" style="font-size:22px;line-height:1">{{ $title }}</span>
            @if($subtitle)
            <span class="eyebrow" style="border-left:0.5px solid var(--line-2);padding-left:10px">{{ $subtitle }}</span>
            @endif
        </div>
    </div>

    <div style="flex:1"></div>

    {{-- Search --}}
    <div x-data="{ open: false }" style="position:relative">
        <div @click="open=true" style="display:flex;align-items:center;gap:8px;height:32px;padding:0 12px;border-radius:999px;background:var(--surface);border:0.5px solid var(--line);width:280px;color:var(--ink-3);cursor:default;">
            <x-ui.icon name="search" size="14"/>
            <span style="font:400 12px/1 var(--f-ui)">Rechercher</span>
            <span style="flex:1"></span>
            <span class="mono" style="font-size:9.5px;padding:2px 5px;border-radius:4px;background:var(--surface-2)">⌘K</span>
        </div>
        <div x-show="open" x-cloak @keydown.escape.window="open=false"
             style="position:fixed;inset:0;z-index:50;background:rgba(0,0,0,0.4);display:flex;align-items:flex-start;justify-content:center;padding-top:80px">
            <div @click.stop style="width:100%;max-width:640px;background:var(--surface);border-radius:16px;box-shadow:var(--sh-lg);overflow:hidden">
                <form action="{{ route('search') }}" method="GET">
                    <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:0.5px solid var(--line)">
                        <x-ui.icon name="search" size="18" style="color:var(--ink-3)"/>
                        <input name="q" autofocus placeholder="Rechercher posts, utilisateurs, hashtags…"
                               style="flex:1;border:0;outline:none;font:400 16px/1 var(--f-ui);color:var(--ink);background:transparent"/>
                        <button type="button" @click="open=false" class="btn btn-ghost" style="height:26px;padding:0 8px;font-size:11px">Esc</button>
                    </div>
                </form>
                <div style="padding:12px 20px 20px;color:var(--ink-3);font:400 13px/1.5 var(--f-ui);text-align:center">
                    Tapez pour rechercher dans UNIVERCONNECT
                </div>
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    <a href="{{ route('notifications.index') }}" class="btn btn-ghost" style="padding:0;width:32px;justify-content:center;position:relative;">
        <x-ui.icon name="bell" size="15"/>
        @if($unread > 0)
        <span style="position:absolute;top:-2px;right:-2px;min-width:16px;height:16px;padding:0 4px;border-radius:999px;background:var(--c-blue);color:#fff;font:600 9px/16px var(--f-mono);text-align:center;border:1.5px solid var(--bg)">{{ $unread > 9 ? '9+' : $unread }}</span>
        @endif
    </a>

    {{-- User avatar / dropdown --}}
    <div x-data="{ open: false }" style="position:relative">
        <div @click="open = !open" style="cursor:pointer">
            <x-ui.avatar
                :name="auth()->user()->name"
                :avatar="auth()->user()->avatar_path ? Storage::url(auth()->user()->avatar_path) : null"
                size="32"
            />
        </div>
        <div x-show="open" @click.outside="open = false" @keydown.escape.window="open = false"
             x-transition:enter=""
             x-cloak
             style="position:absolute;top:100%;right:0;margin-top:8px;min-width:170px;background:var(--surface);border:0.5px solid var(--line);border-radius:10px;box-shadow:var(--sh-lg);padding:6px;z-index:50;">
            <a href="{{ route('profile.show', auth()->user()) }}" style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;font:500 12.5px/1 var(--f-ui);color:var(--ink);text-decoration:none;transition:background 0.14s" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">
                <x-ui.icon name="users" size="14" style="color:var(--ink-3)"/> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="display:flex;align-items:center;gap:8px;width:100%;padding:8px 10px;border:0;border-radius:8px;background:transparent;font:500 12.5px/1 var(--f-ui);color:var(--c-terracotta);text-align:left;cursor:pointer;transition:background 0.14s" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">
                    <x-ui.icon name="logout" size="14" style="color:var(--c-terracotta)"/> Déconnexion
                </button>
            </form>
        </div>
    </div>
</header>
