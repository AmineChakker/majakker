<x-layouts.admin title="Tableau de bord" subtitle="UNIVERCONNECT SA · Plateforme">
<div style="padding:28px 32px 64px;display:flex;flex-direction:column;gap:22px;max-width:1600px;margin:0 auto">

  {{-- ── Header ── --}}
  <header class="adm-fadeup" style="display:flex;align-items:flex-end;gap:28px">
    <div style="flex:1">
      <span class="adm-eyebrow">Plateforme · Semaine {{ now()->weekOfYear }} · {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</span>
      <h1 style="font:400 38px/1.05 var(--f-display);letter-spacing:-.02em;margin:12px 0 0">
        Bonjour {{ auth()->user()->short_name }}. <span class="mj-grad-text">Voici la plateforme aujourd'hui.</span>
      </h1>
    </div>
    <div style="display:flex;gap:8px">
      <button style="display:inline-flex;align-items:center;height:38px;padding:0 16px;border-radius:10px;background:var(--mj-surface);color:var(--mj-ink);border:0.5px solid var(--mj-line-2);font:500 12.5px/1 var(--f-ui);cursor:pointer">
        Exporter le rapport
      </button>
      <a href="{{ route('admin.schools') }}" class="adm-cta">
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
        Inviter une école
      </a>
    </div>
  </header>

  {{-- ── KPI Row ── --}}
  @php
    $kpis = [
      ['icon'=>'classes','accent'=>'linear-gradient(180deg,#7E5BEF,#5B3FE6)','ibg'=>'rgba(126,91,239,.10)','ifg'=>'#7E5BEF',
       'label'=>'Écoles partenaires','value'=>number_format($schoolCount),'prefix'=>null,'suffix'=>null,
       'delta'=>'+8','up'=>true,'sub'=>'3 ce mois',
       'spark'=>[18,22,28,32,38,42,48,52,58,64,72,82,98,112,128,$schoolCount]],
      ['icon'=>'users','accent'=>'linear-gradient(180deg,#3B82F6,#2563EB)','ibg'=>'rgba(37,99,235,.10)','ifg'=>'#2563EB',
       'label'=>'Élèves actifs','value'=>number_format($studentCount),'prefix'=>null,'suffix'=>null,
       'delta'=>'+12,4%','up'=>true,'sub'=>'ce mois',
       'spark'=>[60,62,64,66,68,70,72,75,77,79,81,82,84,85,86,round($studentCount/1000,1)]],
      ['icon'=>'book','accent'=>'linear-gradient(180deg,#06B6D4,#0891B2)','ibg'=>'rgba(6,182,212,.12)','ifg'=>'#0891B2',
       'label'=>'Enseignants','value'=>number_format($teacherCount),'prefix'=>null,'suffix'=>null,
       'delta'=>'+218','up'=>true,'sub'=>'ce mois',
       'spark'=>[2400,2500,2550,2600,2700,2800,2850,2900,3000,3050,3100,3150,3200,3218]],
      ['icon'=>'chart','accent'=>'linear-gradient(180deg,#10B981,#059669)','ibg'=>'rgba(16,185,129,.12)','ifg'=>'#059669',
       'label'=>'MRR','value'=>'412','prefix'=>'DH ','suffix'=>' K',
       'delta'=>'+18%','up'=>true,'sub'=>'vs mois dernier',
       'spark'=>[300,310,318,328,340,348,358,368,378,388,395,402,408,412]],
    ];
  @endphp
  <div class="adm-kpi-row adm-fadeup adm-d1">
    @foreach($kpis as $k)
    @php $sparkMax = max($k['spark']); @endphp
    <div class="adm-kpi" style="--kpi-accent:{{ $k['accent'] }};--kpi-icon-bg:{{ $k['ibg'] }};--kpi-icon-fg:{{ $k['ifg'] }}">
      <div class="adm-kpi-h">
        <div class="adm-kpi-icon"><x-ui.icon name="{{ $k['icon'] }}" size="16"/></div>
        <span class="adm-kpi-label">{{ $k['label'] }}</span>
      </div>
      <div class="adm-kpi-big">
        @if($k['prefix'])<span class="adm-kpi-prefix">{{ $k['prefix'] }}</span>@endif
        {{ $k['value'] }}
        @if($k['suffix'])<span class="adm-kpi-suffix">{{ $k['suffix'] }}</span>@endif
      </div>
      <div class="adm-kpi-bottom">
        <div>
          <span class="adm-kpi-delta {{ $k['up'] ? 'up' : 'down' }}">
            @if($k['up'])<svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 17V5M4 11L10 5L16 11"/></svg>@endif
            {{ $k['delta'] }}
          </span>
          <div class="adm-kpi-sub">{{ $k['sub'] }}</div>
        </div>
        <div class="adm-kpi-spark">
          @foreach($k['spark'] as $i => $v)
          <span style="height:{{ max(round(($v/$sparkMax)*100),4) }}%;{{ $i===count($k['spark'])-1 ? 'background:var(--kpi-icon-fg)' : '' }}"></span>
          @endforeach
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Activity chart + Moderation ── --}}
  <div style="display:grid;grid-template-columns:1.65fr 1fr;gap:16px">

    {{-- SVG Smooth line chart --}}
    <div class="adm-card adm-fadeup adm-d2" style="display:flex;flex-direction:column;gap:12px">
      <header style="display:flex;align-items:baseline;gap:14px">
        <span class="adm-eyebrow">Activité plateforme — 30 derniers jours</span>
        <span style="flex:1"></span>
        <div class="adm-tabs">
          @foreach(['7j','30j','90j','Année'] as $i => $t)
          <button class="adm-tab {{ $i===1?'active':'' }}">{{ $t }}</button>
          @endforeach
        </div>
      </header>
      <div style="display:flex;align-items:flex-end;gap:28px">
        <div>
          <div class="mj-grad-num" style="font:400 38px/1 var(--f-display);letter-spacing:-.02em">{{ number_format($activityData->sum()) }}</div>
          <div style="font:400 11px/1.3 var(--f-ui);color:var(--mj-ink-3);margin-top:6px">Publications · ce mois</div>
        </div>
        <span style="flex:1"></span>
        <div style="display:flex;align-items:center;gap:14px">
          <span style="display:inline-flex;align-items:center;gap:6px;font:500 11px/1 var(--f-ui);color:var(--mj-ink-2)">
            <span style="width:10px;height:10px;border-radius:2px;background:var(--mj-gradient)"></span> Publications
          </span>
        </div>
      </div>
      @php
        $data = $activityData->values()->toArray();
        if (empty(array_filter($data))) { $data = array_map(fn($i) => rand(20,150), range(0,29)); }
        $n = count($data); $W = 700; $H = 200;
        $maxV = max(max($data),1) * 1.1;
        $pts = collect(range(0,$n-1))->map(fn($i) => [
          round(($i/max($n-1,1))*$W,1),
          round($H - ($data[$i]/$maxV)*$H,1),
        ]);
        $linePath = "M {$pts[0][0]} {$pts[0][1]}";
        for ($i = 1; $i < $n; $i++) {
          [$px,$py] = $pts[$i-1]; [$x,$y] = $pts[$i];
          $cpx = round(($px+$x)/2,1);
          $linePath .= " Q {$cpx} {$py}, {$cpx} ".round(($py+$y)/2,1)." T {$x} {$y}";
        }
        $areaPath = $linePath." L {$W} {$H} L 0 {$H} Z";
        $lastPt = $pts->last();
      @endphp
      <div class="adm-chart-wrap">
        <div class="adm-chart-y">
          @foreach(['300','200','100','0'] as $v)<span>{{ $v }}</span>@endforeach
        </div>
        <svg class="adm-chart-svg" viewBox="0 0 {{ $W }} {{ $H }}" preserveAspectRatio="none">
          <defs>
            <linearGradient id="adm-area-g" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#7E5BEF" stop-opacity="0.32"/>
              <stop offset="100%" stop-color="#2563EB" stop-opacity="0"/>
            </linearGradient>
            <linearGradient id="adm-line-g" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0%" stop-color="#7E5BEF"/>
              <stop offset="100%" stop-color="#2563EB"/>
            </linearGradient>
            <pattern id="adm-dot-grid" x="0" y="0" width="40" height="50" patternUnits="userSpaceOnUse">
              <circle cx="0" cy="0" r="0.8" fill="rgba(20,21,43,0.08)"/>
            </pattern>
          </defs>
          <rect width="{{ $W }}" height="{{ $H }}" fill="url(#adm-dot-grid)"/>
          <path d="{{ $areaPath }}" fill="url(#adm-area-g)"/>
          <path d="{{ $linePath }}" stroke="url(#adm-line-g)" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
          <circle cx="{{ $lastPt[0] }}" cy="{{ $lastPt[1] }}" r="5" fill="#fff" stroke="#7E5BEF" stroke-width="2.5"/>
        </svg>
        <div class="adm-chart-x">
          <span>{{ now()->subDays(29)->locale('fr')->isoFormat('D MMM') }}</span>
          <span>{{ now()->subDays(21)->locale('fr')->isoFormat('D MMM') }}</span>
          <span>{{ now()->subDays(14)->locale('fr')->isoFormat('D MMM') }}</span>
          <span>{{ now()->subDays(7)->locale('fr')->isoFormat('D MMM') }}</span>
          <span>{{ now()->locale('fr')->isoFormat('D MMM') }}</span>
        </div>
      </div>
    </div>

    {{-- Moderation panel --}}
    <div class="adm-card adm-fadeup adm-d3" style="display:flex;flex-direction:column;gap:12px">
      <header style="display:flex;align-items:baseline">
        <span class="adm-eyebrow">Modération IA</span>
        <span style="flex:1"></span>
        <span class="adm-tier pro" style="height:20px;font-size:9.5px">{{ $pendingMod }} EN ATTENTE</span>
      </header>
      <div style="display:flex;flex-direction:column;gap:10px">
        @forelse($reports as $report)
        @php
          $sev = $report->severity ?? 'low';
          $sevClass = match($sev) { 'high' => 'high', 'medium' => 'med', default => 'low' };
          $sevLabel = match($sev) { 'high' => 'HAUT', 'medium' => 'MOY.', default => 'BAS' };
        @endphp
        <div class="adm-mod-item">
          <div style="display:flex;align-items:center;gap:10px">
            <x-ui.avatar :name="$report->post?->user?->name ?? '?'" size="28"/>
            <div style="flex:1;min-width:0">
              <div style="font:500 12px/1.2 var(--f-ui);color:var(--mj-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                {{ $report->post?->user?->name ?? 'Utilisateur' }}
                @if($report->post?->user?->school)· {{ $report->post->user->school->name }}@endif
              </div>
            </div>
            <span class="adm-sev {{ $sevClass }}">{{ $sevLabel }}</span>
            @if($report->ai_score)
            <span style="font:500 10.5px/1 var(--f-mono);color:var(--mj-ink-3);letter-spacing:.06em">IA · {{ number_format($report->ai_score,2) }}</span>
            @endif
          </div>
          <div style="font:400 12px/1.5 var(--f-ui);color:var(--mj-ink-2)">{{ $report->reason }}</div>
        </div>
        @empty
        <div style="padding:20px;text-align:center;color:var(--mj-ink-3);font:400 12px/1.5 var(--f-ui)">
          Aucun signalement en attente ✓
        </div>
        @endforelse
      </div>
      <a href="{{ route('admin.moderation') }}" style="display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 14px;border-radius:9px;border:0.5px solid var(--mj-line-2);background:var(--mj-surface);font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);text-decoration:none;align-self:flex-start">
        Voir la file de modération
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M5 10h10M12 7l3 3-3 3"/></svg>
      </a>
    </div>
  </div>

  {{-- ── 3-column bottom row ── --}}
  <div style="display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:16px">

    {{-- Top schools --}}
    <div class="adm-card adm-fadeup adm-d2" style="display:flex;flex-direction:column;gap:14px">
      <header style="display:flex;align-items:baseline">
        <span class="adm-eyebrow">Écoles les plus actives</span>
        <span style="flex:1"></span>
        <a href="{{ route('admin.schools') }}" style="font:500 11px/1 var(--f-ui);color:var(--mj-purple);text-decoration:none">Voir tout</a>
      </header>
      <div style="display:flex;flex-direction:column">
        @php $maxPosts = $topSchools->first()?->posts_count ?: 1; @endphp
        @foreach($topSchools as $i => $school)
        <div class="adm-srow">
          <div class="adm-srow-rank">{{ $i+1 }}</div>
          <div style="min-width:0">
            <div style="display:flex;align-items:baseline;gap:8px">
              <div style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $school->name }}</div>
              <div style="font:400 10.5px/1 var(--f-ui);color:var(--mj-ink-3)">· {{ $school->city }}</div>
              <span style="flex:1"></span>
              <div style="font:500 10.5px/1 var(--f-mono);color:var(--mj-ink-3)">{{ number_format($school->student_count) }}</div>
            </div>
            <div class="adm-bar"><i style="width:{{ $maxPosts > 0 ? round(($school->posts_count/$maxPosts)*100) : 0 }}%"></i></div>
          </div>
          <div style="font:500 12px/1 var(--f-mono);color:var(--mj-ink-2);text-align:right">{{ $school->posts_count }}</div>
        </div>
        @endforeach
        @if($topSchools->isEmpty())
        <div style="padding:20px;text-align:center;color:var(--mj-ink-3);font:400 12px/1 var(--f-ui)">Aucune école</div>
        @endif
      </div>
    </div>

    {{-- Revenue donut --}}
    <div class="adm-card adm-fadeup adm-d3" style="display:flex;flex-direction:column;gap:14px">
      <header><span class="adm-eyebrow">Revenus · {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</span></header>
      @php
        $revSegs = [
          ['label'=>'Pro · Lycées',      'val'=>248,'color'=>'#7E5BEF'],
          ['label'=>'School · Collèges', 'val'=>102,'color'=>'#2563EB'],
          ['label'=>'Starter',           'val'=>38, 'color'=>'#06B6D4'],
          ['label'=>'Add-ons',           'val'=>24, 'color'=>'#10B981'],
        ];
        $revTotal = array_sum(array_column($revSegs,'val'));
        $R = 70; $C = 2*M_PI*$R; $acc = 0;
      @endphp
      <div class="adm-donut">
        <svg width="168" height="168" viewBox="0 0 168 168" style="transform:rotate(-90deg)">
          <circle cx="84" cy="84" r="{{ $R }}" fill="none" stroke="var(--surface-3)" stroke-width="14"/>
          @foreach($revSegs as $seg)
          @php $len=round(($seg['val']/$revTotal)*$C,2); $gap=round($C-$len,2); $off=round(-$acc,2); $acc+=$len; @endphp
          <circle cx="84" cy="84" r="{{ $R }}" fill="none" stroke="{{ $seg['color'] }}" stroke-width="14"
            stroke-dasharray="{{ $len }} {{ $gap }}" stroke-dashoffset="{{ $off }}" stroke-linecap="butt"/>
          @endforeach
        </svg>
        <div class="adm-donut-center">
          <span style="font:500 10px/1 var(--f-mono);letter-spacing:.16em;color:var(--mj-ink-3);text-transform:uppercase">MRR</span>
          <span class="mj-grad-num" style="font:400 30px/1 var(--f-display);letter-spacing:-.02em;margin-top:4px">412 K</span>
          <span style="font:500 10.5px/1 var(--f-mono);color:#047857;letter-spacing:.06em;margin-top:6px">+18% MoM</span>
        </div>
      </div>
      <div>
        @foreach($revSegs as $seg)
        <div class="adm-rev-key">
          <span class="dot" style="background:{{ $seg['color'] }}"></span>
          <div style="flex:1;font:500 11.5px/1.2 var(--f-ui)">{{ $seg['label'] }}</div>
          <span style="font:500 11.5px/1 var(--f-mono);color:var(--mj-ink-2)">{{ $seg['val'] }} K</span>
          <span style="font:500 10px/1 var(--f-mono);color:var(--mj-ink-3);width:36px;text-align:right">{{ round(($seg['val']/$revTotal)*100) }}%</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Recent signups --}}
    <div class="adm-card adm-fadeup adm-d4" style="display:flex;flex-direction:column;gap:14px">
      <header style="display:flex;align-items:baseline">
        <span class="adm-eyebrow">Nouvelles écoles</span>
        <span style="flex:1"></span>
        <span style="font:500 11px/1 var(--f-mono);color:#047857;letter-spacing:.06em">+{{ $recentSchools->count() }}</span>
      </header>
      <div>
        @forelse($recentSchools as $school)
        @php
          $tier  = $school->plan ?? 'starter';
          $tCls  = match($tier) { 'pro'=>'pro','school'=>'school',default=>'starter' };
          $tLbl  = match($tier) { 'pro'=>'PRO','school'=>'SCHOOL',default=>'STARTER' };
        @endphp
        <div class="adm-signup">
          <div class="adm-signup-flag">{{ $school->initial }}</div>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $school->name }}</div>
            <div style="font:400 10.5px/1.2 var(--f-ui);color:var(--mj-ink-3);margin-top:2px">{{ $school->city }} · {{ $school->created_at->locale('fr')->isoFormat('D MMM') }}</div>
          </div>
          <span class="adm-tier {{ $tCls }}">{{ $tLbl }}</span>
        </div>
        @empty
        <div style="padding:20px;text-align:center;color:var(--mj-ink-3);font:400 12px/1 var(--f-ui)">Aucune école récente</div>
        @endforelse
      </div>
    </div>

  </div>
</div>
</x-layouts.admin>
