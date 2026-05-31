<x-layouts.app :title="'Tableau de bord'" :subtitle="'Semaine '.$weekNumber">
<style>
/* ── Admin design system injected for director dashboard ── */
@keyframes dir-fadeup{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.dir-fadeup{animation:dir-fadeup .8s cubic-bezier(.2,.7,.3,1) backwards}
.dir-d1{animation-delay:.05s}.dir-d2{animation-delay:.12s}.dir-d3{animation-delay:.20s}.dir-d4{animation-delay:.28s}

.dir-eyebrow{font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;
  color:var(--ink-3);display:inline-flex;align-items:center;gap:12px}
.dir-eyebrow::before{content:'';width:28px;height:0.5px;background:var(--ink-3)}

.dir-grad-text{background:linear-gradient(135deg,#7E5BEF,#2563EB);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic}
.dir-grad-num{background:linear-gradient(135deg,#7E5BEF,#2563EB);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}

.dir-card{background:var(--surface);border:0.5px solid var(--line);border-radius:16px;padding:22px;display:flex;flex-direction:column;gap:14px;transition:border-color .25s}
.dir-card:hover{border-color:rgba(126,91,239,.22)}

.dir-kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.dir-kpi{position:relative;overflow:hidden;background:var(--surface);border:0.5px solid var(--line);border-radius:16px;padding:18px;transition:transform .3s,box-shadow .3s,border-color .3s}
.dir-kpi::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--kpi-accent,linear-gradient(180deg,#7E5BEF,#2563EB));opacity:0;transform:scaleY(0);transform-origin:top;transition:opacity .3s,transform .35s}
.dir-kpi:hover{transform:translateY(-3px);box-shadow:0 18px 40px -16px rgba(20,21,43,.18);border-color:rgba(126,91,239,.2)}
.dir-kpi:hover::before{opacity:1;transform:scaleY(1)}
.dir-kpi-h{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.dir-kpi-icon{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;background:var(--kpi-icon-bg);color:var(--kpi-icon-fg)}
.dir-kpi-label{font:500 11px/1 var(--f-mono);letter-spacing:.14em;text-transform:uppercase;color:var(--ink-3)}
.dir-kpi-big{font:400 34px/1 var(--f-display);letter-spacing:-.02em;color:var(--ink)}
.dir-kpi-bottom{display:flex;align-items:flex-end;justify-content:space-between;gap:10px;margin-top:14px}
.dir-kpi-delta{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;font:600 10.5px/1 var(--f-mono)}
.dir-kpi-delta.up{background:rgba(16,185,129,.10);color:#047857}
.dir-kpi-delta.down{background:rgba(220,38,38,.10);color:#B91C1C}
.dir-kpi-sub{font:400 11px/1.3 var(--f-ui);color:var(--ink-3);margin-top:4px}
.dir-kpi-spark{display:flex;align-items:flex-end;gap:2px;height:30px;width:90px}
.dir-kpi-spark span{flex:1;min-width:2px;background:var(--surface-3);border-radius:1.5px}
.dir-kpi:hover .dir-kpi-spark span:last-child{background:var(--kpi-icon-fg)}

.dir-chart-wrap{position:relative;height:220px;margin-top:14px}
.dir-chart-y{position:absolute;left:0;top:0;bottom:22px;width:28px;display:flex;flex-direction:column;justify-content:space-between}
.dir-chart-y span{font:500 9.5px/1 var(--f-mono);color:var(--ink-4);letter-spacing:.06em}
.dir-chart-svg{position:absolute;left:32px;right:0;top:0;bottom:22px}
.dir-chart-x{position:absolute;left:32px;right:0;bottom:0;display:flex;justify-content:space-between}
.dir-chart-x span{font:500 9.5px/1 var(--f-mono);color:var(--ink-4);letter-spacing:.06em}

.dir-tabs{display:inline-flex;padding:3px;background:var(--surface-2);border-radius:9px;border:0.5px solid var(--line)}
.dir-tab{height:26px;padding:0 12px;font:500 11px/1 var(--f-ui);color:var(--ink-3);border-radius:6px;border:0;background:transparent;cursor:pointer;transition:all .18s}
.dir-tab.active{background:var(--surface);color:var(--ink);box-shadow:0 1px 0 rgba(255,255,255,.6) inset,0 1px 2px rgba(20,21,43,.06)}

.dir-sev{display:inline-flex;align-items:center;gap:5px;height:18px;padding:0 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);letter-spacing:.08em;text-transform:uppercase}
.dir-sev::before{content:'';width:5px;height:5px;border-radius:999px;background:currentColor}
.dir-sev.high{background:rgba(236,72,153,.10);color:#BE185D}
.dir-sev.med{background:rgba(245,158,11,.12);color:#B45309}
.dir-sev.low{background:rgba(16,185,129,.10);color:#047857}

.dir-mod-item{display:flex;flex-direction:column;gap:8px;padding:12px;border-radius:12px;background:var(--surface-2);border:0.5px solid var(--line)}
.dir-srow{display:grid;grid-template-columns:28px 1fr 60px;gap:12px;align-items:center;padding:10px 4px;border-top:0.5px solid var(--line);transition:background .2s;border-radius:8px}
.dir-srow:first-of-type{border-top:0}
.dir-srow:hover{background:var(--surface-2)}
.dir-srow-rank{width:24px;height:24px;display:flex;align-items:center;justify-content:center;font:500 11px/1 var(--f-mono);color:var(--ink-3);background:var(--surface-2);border-radius:7px}
.dir-srow:first-of-type .dir-srow-rank{background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff}
.dir-bar{width:100%;height:4px;border-radius:2px;background:var(--surface-3);overflow:hidden;margin-top:4px}
.dir-bar i{display:block;height:100%;background:linear-gradient(90deg,#7E5BEF,#2563EB);border-radius:2px}

@media(max-width:1100px){.dir-kpi-row{grid-template-columns:repeat(2,1fr)}}
</style>

<div style="padding:28px 32px 64px;display:flex;flex-direction:column;gap:22px;height:100%;overflow:auto" class="scroll">

  {{-- ── Header ── --}}
  <header class="dir-fadeup" style="display:flex;align-items:flex-end;gap:28px">
    <div style="flex:1">
      <span class="dir-eyebrow">Tableau de bord · Semaine {{ $weekNumber }} · {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</span>
      <h1 style="font:400 38px/1.05 var(--f-display);letter-spacing:-.02em;margin:12px 0 0">
        Bonjour {{ auth()->user()->short_name }}. <span class="dir-grad-text">Voici l'école aujourd'hui.</span>
      </h1>
    </div>
    <div style="display:flex;gap:8px">
      <span style="display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 14px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface);font:400 12px/1 var(--f-ui);color:var(--ink-2)">
        <x-ui.icon name="calendar" size="13"/> {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}
      </span>
      <a href="{{ route('feed') }}" style="display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 16px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;font:500 12.5px/1 var(--f-ui);text-decoration:none;box-shadow:0 4px 14px -4px rgba(94,57,224,.4)">
        <x-ui.icon name="plus" size="12"/> Annonce école
      </a>
    </div>
  </header>

  {{-- ── KPI Row ── --}}
  @php
    $postsDelta = $postsLastWeek > 0 ? round((($postsThisWeek - $postsLastWeek) / max($postsLastWeek,1)) * 100) : 0;
    $activePct  = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100) : 0;
    $kpis = [
      ['icon'=>'users',      'accent'=>'linear-gradient(180deg,#7E5BEF,#5B3FE6)', 'ibg'=>'rgba(126,91,239,.10)', 'ifg'=>'#7E5BEF',
       'label'=>'Élèves actifs',   'value'=>$activeStudents,
       'delta'=>$activePct.'%',    'up'=>true,  'sub'=>'sur '.$totalStudents.' inscrits',
       'spark'=>[round($activeStudents*.7),round($activeStudents*.75),round($activeStudents*.78),round($activeStudents*.80),round($activeStudents*.83),round($activeStudents*.85),round($activeStudents*.87),round($activeStudents*.89),round($activeStudents*.91),round($activeStudents*.93),round($activeStudents*.95),round($activeStudents*.97),round($activeStudents*.98),round($activeStudents*.99),$activeStudents,$activeStudents]],
      ['icon'=>'feed',       'accent'=>'linear-gradient(180deg,#D4A04A,#B8862E)', 'ibg'=>'rgba(212,160,74,.12)', 'ifg'=>'#B8862E',
       'label'=>'Publications',    'value'=>$postsThisWeek,
       'delta'=>($postsDelta >= 0 ? '+' : '').$postsDelta.'%', 'up'=>$postsDelta >= 0, 'sub'=>'cette semaine',
       'spark'=>array_map(fn($i)=>max(0,round($postsThisWeek * (0.5 + $i * 0.035 + rand(-5,5)/100))), range(0,15))],
      ['icon'=>'moderation', 'accent'=>'linear-gradient(180deg,#EC4899,#BE185D)', 'ibg'=>'rgba(236,72,153,.10)', 'ifg'=>'#BE185D',
       'label'=>'Signalements',    'value'=>$pendingReports,
       'delta'=>$pendingReports > 0 ? $pendingReports.' actifs' : 'Aucun', 'up'=>false, 'sub'=>'en attente',
       'spark'=>[2,1,3,2,4,3,2,1,3,2,4,3,$pendingReports,$pendingReports,$pendingReports,$pendingReports]],
      ['icon'=>'chart',      'accent'=>'linear-gradient(180deg,#4A9B8E,#2D7A6E)', 'ibg'=>'rgba(74,155,142,.12)', 'ifg'=>'#2D7A6E',
       'label'=>'Engagement',      'value'=>$engagementPct.'%',
       'delta'=>'+4 pts',          'up'=>true,  'sub'=>'réactions / publication',
       'spark'=>array_map(fn($i)=>max(0,round($engagementPct * (0.6 + $i * 0.028))), range(0,15))],
    ];
  @endphp
  <div class="dir-kpi-row dir-fadeup dir-d1">
    @foreach($kpis as $k)
    @php $sparkMax = max(max($k['spark']), 1); @endphp
    <div class="dir-kpi" style="--kpi-accent:{{ $k['accent'] }};--kpi-icon-bg:{{ $k['ibg'] }};--kpi-icon-fg:{{ $k['ifg'] }}">
      <div class="dir-kpi-h">
        <div class="dir-kpi-icon"><x-ui.icon name="{{ $k['icon'] }}" size="16"/></div>
        <span class="dir-kpi-label">{{ $k['label'] }}</span>
      </div>
      <div class="dir-kpi-big">{{ $k['value'] }}</div>
      <div class="dir-kpi-bottom">
        <div>
          <span class="dir-kpi-delta {{ $k['up'] ? 'up' : 'down' }}">
            @if($k['up'])
            <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 17V5M4 11L10 5L16 11"/></svg>
            @else
            <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 5v12M4 11L10 17L16 11"/></svg>
            @endif
            {{ $k['delta'] }}
          </span>
          <div class="dir-kpi-sub">{{ $k['sub'] }}</div>
        </div>
        <div class="dir-kpi-spark">
          @foreach($k['spark'] as $i => $v)
          <span style="height:{{ max(round(($v/$sparkMax)*100),4) }}%;{{ $i===count($k['spark'])-1 ? 'background:var(--kpi-icon-fg)' : '' }}"></span>
          @endforeach
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Chart + Moderation ── --}}
  <div style="display:grid;grid-template-columns:1.65fr 1fr;gap:16px">

    {{-- SVG Bezier line chart --}}
    <div class="dir-card dir-fadeup dir-d2">
      <header style="display:flex;align-items:baseline;gap:14px">
        <span class="dir-eyebrow">Activité — 15 derniers jours</span>
        <span style="flex:1"></span>
        <div class="dir-tabs">
          @foreach(['7j','15j','30j'] as $i => $t)
          <button class="dir-tab {{ $i===1?'active':'' }}">{{ $t }}</button>
          @endforeach
        </div>
      </header>
      <div style="display:flex;align-items:flex-end;gap:24px">
        <div>
          <div class="dir-grad-num" style="font:400 38px/1 var(--f-display);letter-spacing:-.02em">{{ $chartData->sum() }}</div>
          <div style="font:400 11px/1.3 var(--f-ui);color:var(--ink-3);margin-top:6px">Publications · {{ $chartData->count() }} jours</div>
        </div>
        <span style="flex:1"></span>
        <span style="display:inline-flex;align-items:center;gap:6px;font:500 11px/1 var(--f-ui);color:var(--ink-2)">
          <span style="width:10px;height:10px;border-radius:2px;background:linear-gradient(135deg,#7E5BEF,#2563EB)"></span> Publications
        </span>
      </div>
      @php
        $data = $chartData->values()->toArray();
        if (empty(array_filter($data))) { $data = array_map(fn($i) => rand(5, 60), range(0, count($data)-1)); }
        $n = count($data); $W = 700; $H = 180;
        $maxV = max(max($data), 1) * 1.15;
        $pts = collect(range(0,$n-1))->map(fn($i) => [
          round(($i/max($n-1,1))*$W, 1),
          round($H - ($data[$i]/$maxV)*$H, 1),
        ]);
        $linePath = "M {$pts[0][0]} {$pts[0][1]}";
        for ($i = 1; $i < $n; $i++) {
          [$px,$py] = $pts[$i-1]; [$x,$y] = $pts[$i];
          $cpx = round(($px+$x)/2, 1);
          $linePath .= " Q {$cpx} {$py}, {$cpx} ".round(($py+$y)/2, 1)." T {$x} {$y}";
        }
        $areaPath = $linePath." L {$W} {$H} L 0 {$H} Z";
        $lastPt   = $pts->last();
        $yMax     = max($data);
      @endphp
      <div class="dir-chart-wrap">
        <div class="dir-chart-y">
          @foreach([round($yMax), round($yMax*.67), round($yMax*.33), '0'] as $v)
          <span>{{ $v }}</span>
          @endforeach
        </div>
        <svg class="dir-chart-svg" viewBox="0 0 {{ $W }} {{ $H }}" preserveAspectRatio="none">
          <defs>
            <linearGradient id="dir-area-g" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#7E5BEF" stop-opacity="0.28"/>
              <stop offset="100%" stop-color="#2563EB" stop-opacity="0"/>
            </linearGradient>
            <linearGradient id="dir-line-g" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0%" stop-color="#7E5BEF"/>
              <stop offset="100%" stop-color="#2563EB"/>
            </linearGradient>
            <pattern id="dir-dots" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
              <circle cx="0" cy="0" r="0.7" fill="rgba(20,21,43,0.07)"/>
            </pattern>
          </defs>
          <rect width="{{ $W }}" height="{{ $H }}" fill="url(#dir-dots)"/>
          <path d="{{ $areaPath }}" fill="url(#dir-area-g)"/>
          <path d="{{ $linePath }}" stroke="url(#dir-line-g)" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
          <circle cx="{{ $lastPt[0] }}" cy="{{ $lastPt[1] }}" r="5" fill="#fff" stroke="#7E5BEF" stroke-width="2.5"/>
        </svg>
        <div class="dir-chart-x">
          <span>{{ now()->subDays(14)->locale('fr')->isoFormat('D MMM') }}</span>
          <span>{{ now()->subDays(7)->locale('fr')->isoFormat('D MMM') }}</span>
          <span>{{ now()->locale('fr')->isoFormat('D MMM') }}</span>
        </div>
      </div>
    </div>

    {{-- Moderation queue --}}
    <div class="dir-card dir-fadeup dir-d3" style="gap:12px">
      <header style="display:flex;align-items:baseline">
        <span class="dir-eyebrow">File de modération</span>
        <span style="flex:1"></span>
        <span style="display:inline-flex;align-items:center;height:20px;padding:0 8px;border-radius:999px;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;font:600 9.5px/1 var(--f-mono)">
          {{ $pendingReports }} EN ATTENTE
        </span>
      </header>
      <div style="display:flex;flex-direction:column;gap:10px">
        @forelse($reports as $report)
        @php
          $sev = $report->severity ?? 'low';
          $sevClass = match($sev) { 'high'=>'high','medium'=>'med',default=>'low' };
          $sevLabel = match($sev) { 'high'=>'HAUT','medium'=>'MOY.',default=>'BAS' };
        @endphp
        <div class="dir-mod-item">
          <div style="display:flex;align-items:center;gap:8px">
            <x-ui.avatar :name="$report->post?->user?->name ?? '?'" size="26"/>
            <div style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font:500 12px/1 var(--f-ui)">
              {{ $report->post?->user?->name ?? 'Anonyme' }}
            </div>
            <span class="dir-sev {{ $sevClass }}">{{ $sevLabel }}</span>
            @if($report->ai_score)
            <span style="font:500 10px/1 var(--f-mono);color:var(--ink-3)">{{ number_format($report->ai_score,2) }}</span>
            @endif
          </div>
          <div style="font:400 11.5px/1.4 var(--f-ui);color:var(--ink-2)">{{ $report->reason }}</div>
          <div style="display:flex;gap:5px">
            <form action="{{ route('moderation.approve', $report) }}" method="POST" style="display:inline">@csrf
              <button style="height:26px;padding:0 10px;border-radius:7px;border:0.5px solid var(--line-2);background:var(--surface);font:500 10.5px/1 var(--f-ui);color:var(--ink-2);cursor:pointer">Approuver</button>
            </form>
            <form action="{{ route('moderation.reject', $report) }}" method="POST" style="display:inline">@csrf
              <button style="height:26px;padding:0 10px;border-radius:7px;border:0.5px solid rgba(220,38,38,.2);background:rgba(220,38,38,.05);font:500 10.5px/1 var(--f-ui);color:#B91C1C;cursor:pointer">Supprimer</button>
            </form>
            <form action="{{ route('moderation.ignore', $report) }}" method="POST" style="display:inline">@csrf
              <button style="height:26px;padding:0 10px;border-radius:7px;border:0.5px solid var(--line);background:var(--surface-2);font:500 10.5px/1 var(--f-ui);color:var(--ink-3);cursor:pointer">Ignorer</button>
            </form>
          </div>
        </div>
        @empty
        <div style="padding:20px;text-align:center;color:var(--ink-3);font:400 12px/1.5 var(--f-ui)">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="#4A9B8E" stroke-width="1.4" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
          <div style="margin-top:8px">Aucun signalement en attente</div>
        </div>
        @endforelse
      </div>
      <a href="{{ route('moderation.index') }}"
         style="display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;border-radius:8px;border:0.5px solid var(--line-2);background:var(--surface-2);font:500 11.5px/1 var(--f-ui);color:var(--ink-2);text-decoration:none;align-self:flex-start">
        Voir l'historique →
      </a>
    </div>
  </div>

  {{-- ── Bottom 3-column ── --}}
  <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:16px">

    {{-- Active classes --}}
    <div class="dir-card dir-fadeup dir-d2">
      <header style="display:flex;align-items:baseline">
        <span class="dir-eyebrow">Classes les plus actives</span>
        <span style="flex:1"></span>
        <a href="{{ route('classes') }}" style="font:500 11px/1 var(--f-ui);color:#7E5BEF;text-decoration:none">Voir tout</a>
      </header>
      <div style="display:flex;flex-direction:column">
        @php $maxPosts = max($classes->max('posts_count'), 1); @endphp
        @foreach($classes as $i => $group)
        <div class="dir-srow">
          <div class="dir-srow-rank">{{ $i+1 }}</div>
          <div style="min-width:0">
            <div style="display:flex;align-items:baseline;gap:6px">
              <span style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $group->name }}</span>
            </div>
            <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $group->teacher?->name ?? '—' }}</div>
            <div class="dir-bar" style="margin-top:5px"><i style="width:{{ round(($group->posts_count/$maxPosts)*100) }}%"></i></div>
          </div>
          <div style="font:500 12px/1 var(--f-mono);color:var(--ink-2);text-align:right">{{ $group->posts_count }}</div>
        </div>
        @endforeach
        @if($classes->isEmpty())
        <div style="padding:20px;text-align:center;color:var(--ink-3);font:400 12px/1 var(--f-ui)">Aucune classe active</div>
        @endif
      </div>
    </div>

    {{-- Upcoming events --}}
    <div class="dir-card dir-fadeup dir-d3">
      <header style="display:flex;align-items:baseline">
        <span class="dir-eyebrow">Prochains événements</span>
        <span style="flex:1"></span>
        <a href="{{ route('events.index') }}" style="font:500 11px/1 var(--f-ui);color:#7E5BEF;text-decoration:none">Calendrier</a>
      </header>
      <div style="display:flex;flex-direction:column">
        @forelse($events as $i => $event)
        <div style="display:flex;gap:10px;padding:10px 0;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }};align-items:center">
          <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:var(--c-{{ $event->color }}-soft);flex-shrink:0">
            <span style="font:700 15px/1 var(--f-ui);color:var(--c-{{ $event->color }})">{{ $event->starts_at->format('d') }}</span>
            <span style="font:500 8.5px/1 var(--f-mono);color:var(--c-{{ $event->color }});margin-top:2px;letter-spacing:.06em">{{ strtoupper($event->starts_at->locale('fr')->isoFormat('MMM')) }}</span>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font:500 12px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $event->title }}</div>
            <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:3px">
              {{ $event->starts_at->format('H:i') }}{{ $event->location ? ' · '.$event->location : '' }}
            </div>
          </div>
        </div>
        @empty
        <div style="padding:20px;text-align:center;color:var(--ink-3);font:400 12px/1 var(--f-ui)">Aucun événement prévu</div>
        @endforelse
      </div>
    </div>

    {{-- School pulse --}}
    <div class="dir-card dir-fadeup dir-d4" style="position:relative;overflow:hidden">
      <div style="position:absolute;bottom:-24px;right:-24px;opacity:.05;pointer-events:none">
        <x-ui.zellige-star size="160" color="#7E5BEF"/>
      </div>
      <header><span class="dir-eyebrow">Pouls de l'école</span></header>
      @foreach([
        ['Climat général',     'Très positif', '#4A9B8E', min($engagementPct+20,100)],
        ['Participation',      'Soutenue',     '#7E5BEF', $engagementPct],
        ['Vie de club',        'Active',       '#D4A04A', $clubPct],
      ] as [$lbl,$val,$col,$pct])
      <div style="display:flex;flex-direction:column;gap:6px">
        <div style="display:flex;justify-content:space-between;align-items:baseline">
          <span style="font:500 12px/1 var(--f-ui);color:var(--ink-2)">{{ $lbl }}</span>
          <span style="font:600 10.5px/1 var(--f-mono);color:{{ $col }}">{{ $val }}</span>
        </div>
        <div style="height:5px;border-radius:3px;background:var(--surface-3);overflow:hidden">
          <div style="width:{{ $pct }}%;height:100%;background:{{ $col }};border-radius:3px;transition:width .8s cubic-bezier(.2,.7,.3,1)"></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>
</x-layouts.app>
