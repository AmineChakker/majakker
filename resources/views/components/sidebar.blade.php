@php
$user = auth()->user();
$role = $user->role;
$currentRoute = request()->route()->getName() ?? '';

$navGroups = match($role) {
    'student' => [
        ['hd' => 'Espace', 'items' => [
            ['route' => 'feed', 'label' => 'Fil d\'actualité', 'icon' => 'feed'],
            ['route' => 'classes', 'label' => 'Mes classes', 'icon' => 'classes'],
            ['route' => 'clubs', 'label' => 'Clubs', 'icon' => 'clubs'],
            ['route' => 'messages', 'label' => 'Messages', 'icon' => 'msg'],
        ]],
        ['hd' => 'Personnel', 'items' => [
            ['route' => 'profile.show', 'label' => 'Profil', 'icon' => 'users', 'param' => $user],
            ['route' => 'calendar', 'label' => 'Calendrier', 'icon' => 'calendar'],
        ]],
    ],
    'teacher' => [
        ['hd' => 'Enseignement', 'items' => [
            ['route' => 'feed', 'label' => 'Fil de l\'école', 'icon' => 'feed'],
            ['route' => 'classes', 'label' => 'Mes cours', 'icon' => 'classes'],
            ['route' => 'homework', 'label' => 'Devoirs', 'icon' => 'book'],
            ['route' => 'messages', 'label' => 'Messages', 'icon' => 'msg'],
        ]],
        ['hd' => 'Outils', 'items' => [
            ['route' => 'analytics', 'label' => 'Analyse', 'icon' => 'chart'],
            ['route' => 'calendar', 'label' => 'Planning', 'icon' => 'calendar'],
        ]],
    ],
    'director' => [
        ['hd' => 'Vue d\'ensemble', 'items' => [
            ['route' => 'dashboard', 'label' => 'Tableau de bord', 'icon' => 'grid'],
            ['route' => 'feed', 'label' => 'Fil de l\'école', 'icon' => 'feed'],
        ]],
        ['hd' => 'Gestion scolaire', 'items' => [
            ['route' => 'dashboard.filieres', 'label' => 'Filières', 'icon' => 'book'],
            ['route' => 'dashboard.classes',  'label' => 'Classes',  'icon' => 'classes'],
            ['route' => 'dashboard.teachers', 'label' => 'Enseignants', 'icon' => 'users'],
            ['route' => 'dashboard.students', 'label' => 'Élèves',   'icon' => 'users'],
        ]],
        ['hd' => 'Administration', 'items' => [
            ['route' => 'events.index',      'label' => 'Événements',  'icon' => 'calendar'],
            ['route' => 'moderation.index',  'label' => 'Modération',  'icon' => 'moderation'],
            ['route' => 'analytics',         'label' => 'Analyse',     'icon' => 'chart'],
        ]],
    ],
    'admin' => [
        ['hd' => 'Plateforme', 'items' => [
            ['route' => 'admin.dashboard', 'label' => 'Tableau de bord', 'icon' => 'grid'],
            ['route' => 'admin.schools', 'label' => 'Écoles', 'icon' => 'classes'],
            ['route' => 'admin.users', 'label' => 'Utilisateurs', 'icon' => 'users'],
            ['route' => 'admin.revenue', 'label' => 'Revenus', 'icon' => 'chart'],
        ]],
        ['hd' => 'Système', 'items' => [
            ['route' => 'admin.moderation', 'label' => 'Modération IA', 'icon' => 'moderation'],
            ['route' => 'admin.reports', 'label' => 'Rapports', 'icon' => 'flag'],
        ]],
    ],
    default => [],
};
@endphp

<aside class="app-sidebar" :class="{ open: sidebarOpen }" style="width:240px;height:100%;background:var(--bg);border-right:0.5px solid var(--line);padding:16px 12px 14px;display:flex;flex-direction:column;gap:16px;flex-shrink:0;overflow:hidden;">

    {{-- Brand --}}
    <div style="display:flex;align-items:center;gap:9px;padding:2px 6px">
        <div style="width:26px;height:26px;border-radius:7px;background:var(--ink);color:var(--bg);display:flex;align-items:center;justify-content:center;">
            <x-ui.zellige-star size="20" color="var(--bg)" opacity="0.95"/>
        </div>
        <div style="display:flex;flex-direction:column;line-height:1.1">
            <span style="font:600 13.5px/1 var(--f-ui);letter-spacing:-0.01em">UniverConnect</span>
            <span style="font:500 9.5px/1 var(--f-mono);color:var(--ink-3);letter-spacing:0.08em;margin-top:3px">BETA · MA</span>
        </div>
    </div>

    {{-- School card --}}
    @if($user->school)
    <div style="padding:10px 12px;border-radius:10px;background:var(--surface);border:0.5px solid var(--line);display:flex;align-items:center;gap:10px;">
        <div style="width:28px;height:28px;border-radius:7px;background:var(--c-saffron-soft);color:#8A6520;display:flex;align-items:center;justify-content:center;font-family:var(--f-display);font-size:15px;">
            {{ $user->school->initial }}
        </div>
        <div style="flex:1;min-width:0">
            <div style="font:500 11.5px/1.2 var(--f-ui);color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $user->school->name }}</div>
            <div style="font:400 10px/1.2 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $user->school->city }} · {{ number_format($user->school->student_count) }} élèves</div>
        </div>
        <x-ui.icon name="chevronDown" size="12" style="color:var(--ink-3)"/>
    </div>
    @endif

    {{-- Navigation --}}
    <nav style="display:flex;flex-direction:column;gap:16px;overflow:auto;flex:1" class="scroll">
        @foreach($navGroups as $group)
        <div style="display:flex;flex-direction:column;gap:1px">
            <div class="eyebrow" style="padding:2px 10px 6px;font-size:9.5px">{{ $group['hd'] }}</div>
            @foreach($group['items'] as $item)
            @php
                $isActive = str_starts_with($currentRoute, $item['route']);
                try {
                    $href = isset($item['param']) ? route($item['route'], $item['param']) : route($item['route']);
                } catch(\Exception $e) {
                    $href = '#';
                }
            @endphp
            <a href="{{ $href }}" class="nav-item {{ $isActive ? 'active' : '' }}" style="text-decoration:none">
                <x-ui.icon name="{{ $item['icon'] }}" size="15" style="color:{{ $isActive ? 'var(--ink)' : 'var(--ink-3)' }}"/>
                <span>{{ $item['label'] }}</span>
            </a>
            @endforeach
        </div>
        @endforeach
    </nav>

    {{-- User footer --}}
    <div x-data="{ open: false }" style="position:relative">
        <div @click="open = !open"
             style="display:flex;align-items:center;gap:9px;padding:8px;border-radius:10px;background:var(--surface-2);cursor:pointer;transition:background .14s"
             :style="open ? 'background:var(--surface-3)' : ''">
            <x-ui.avatar :name="$user->name" size="26"/>
            <div style="flex:1;min-width:0">
                <div style="font:500 11.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $user->short_name }}</div>
                <div style="font:400 10px/1.2 var(--f-ui);color:var(--ink-3);margin-top:1px">{{ $user->role_label }}</div>
            </div>
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--ink-3)" stroke-width="1.5" stroke-linecap="round"
                 :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s;flex-shrink:0">
                <path d="M5 8l5 5 5-5"/>
            </svg>
        </div>

        {{-- Dropdown menu --}}
        <div x-show="open" x-cloak @click.outside="open = false" x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
             style="position:absolute;bottom:calc(100% + 6px);left:0;right:0;background:var(--surface);border:0.5px solid var(--line-2);border-radius:10px;box-shadow:var(--sh-lg);overflow:hidden;z-index:50">
            <a href="{{ route('profile.show', $user) }}"
               style="display:flex;align-items:center;gap:9px;padding:9px 12px;font:400 12.5px/1 var(--f-ui);color:var(--ink-2);text-decoration:none;transition:background .12s"
               onmouseenter="this.style.background='var(--surface-2)'" onmouseleave="this.style.background=''">
                <x-ui.icon name="users" size="13" style="color:var(--ink-3)"/>
                Mon profil
            </a>
            <a href="{{ route('profile.edit') }}"
               style="display:flex;align-items:center;gap:9px;padding:9px 12px;font:400 12.5px/1 var(--f-ui);color:var(--ink-2);text-decoration:none;transition:background .12s"
               onmouseenter="this.style.background='var(--surface-2)'" onmouseleave="this.style.background=''">
                <x-ui.icon name="settings" size="13" style="color:var(--ink-3)"/>
                Paramètres
            </a>
            <div style="height:0.5px;background:var(--line);margin:2px 0"></div>
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
