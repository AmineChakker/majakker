<x-layouts.app :title="'Tableau de bord'" :subtitle="'Semaine '.$weekNumber">
<style>
.dash-content{padding:26px 32px 60px;display:flex;flex-direction:column;gap:22px;overflow:auto;height:100%}
.kpi-card{position:relative;overflow:hidden;background:var(--surface);border-radius:var(--r-md);border:0.5px solid var(--line);box-shadow:var(--sh-sm);padding:16px}
.dash-card{background:var(--surface);border-radius:var(--r-md);border:0.5px solid var(--line);box-shadow:var(--sh-sm);padding:18px;display:flex;flex-direction:column;gap:14px}
</style>

<div class="dash-content scroll">

  {{-- Header --}}
  <header style="display:flex;align-items:flex-end;gap:24px;margin-bottom:4px">
    <div style="flex:1">
      <span class="eyebrow">Tableau de bord — semaine {{ $weekNumber }}</span>
      <h1 class="serif" style="font:400 38px/1.05 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        Bonjour {{ auth()->user()->name }}. <span style="color:var(--ink-3)">Voici l'école aujourd'hui.</span>
      </h1>
    </div>
    <div style="display:flex;gap:8px">
      <span class="btn btn-ghost"><x-ui.icon name="calendar" size="13"/> {{ now()->format('d M Y') }}</span>
      <a href="{{ route('posts.store') }}" class="btn btn-primary" style="text-decoration:none">
        <x-ui.icon name="plus" size="12"/> Annonce école
      </a>
    </div>
  </header>

  {{-- KPI strip --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
    @php
    $kpis = [
      ['Élèves actifs', $activeStudents, 'sur '.$totalStudents.' inscrits', 'blue', '+'.round(($activeStudents/max($totalStudents,1))*100).'%'],
      ['Enseignants actifs', $activeTeachers, 'sur '.$totalTeachers.' inscrits', 'blue', '+'.round(($activeTeachers/max($totalTeachers,1))*100).'%'],
      ['Publications', $postsThisWeek, 'cette semaine', 'saffron', ($postsLastWeek>0?'+'.round((($postsThisWeek-$postsLastWeek)/max($postsLastWeek,1))*100).'%':'—')],
      ['Élèves suspendus', $suspendedStudents, 'sur '.$totalStudents.' inscrits', 'blue', '+'.round(($suspendedStudents/max($totalStudents,1))*100).'%'],
    
    ];
    @endphp
    @foreach($kpis as [$lbl,$big,$sub,$tone,$delta])
    <div class="kpi-card">
      <div style="position:absolute;top:-10px;right:-10px;color:var(--c-{{ $tone }});opacity:.18">
        <x-ui.zellige-star size="64"/>
      </div>
      <div class="eyebrow">{{ $lbl }}</div>
      <div style="display:flex;align-items:baseline;gap:8px;margin-top:8px">
        <span style="font:500 28px/1 var(--f-display);letter-spacing:-.01em">{{ $big }}</span>
        <span style="font:500 11px/1 var(--f-mono);color:var(--c-{{ $tone }})">{{ $delta }}</span>
      </div>
      <div style="font:400 11px/1.3 var(--f-ui);color:var(--ink-3);margin-top:4px">{{ $sub }}</div>
    </div>
    @endforeach
  </div>

  {{-- Main grid --}}
  <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:16px">

    {{-- Activity chart --}}
    <div class="dash-card">
      <header style="display:flex;align-items:baseline;gap:12px">
        <span class="eyebrow">Activité — 15 derniers jours</span>
        <span style="flex:1"></span>
        <span class="chip chip-blue">Publications</span>
      </header>
      <div style="display:flex;align-items:baseline;gap:16px">
        <span style="font:400 36px/1 var(--f-display)">{{ $postsThisWeek }}</span>
        <span style="font:500 12px/1 var(--f-mono);color:var(--c-atlas)">cette semaine</span>
      </div>
      @php $maxVal = max($chartData->max(), 1); @endphp
      <div style="display:flex;align-items:flex-end;gap:6px;height:110px;padding:0 2px">
        @foreach($chartData as $i => $val)
        @php
          $pct = round(($val/$maxVal)*100);
          $isRecent = $i >= count($chartData)-3;
          $isLast   = $i === count($chartData)-1;
          $bg = $isLast ? 'var(--c-blue)' : ($isRecent ? 'rgba(61,90,254,.55)' : 'var(--surface-3)');
        @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center">
          <div style="width:100%;height:{{ max($pct,2) }}%;border-radius:4px 4px 1px 1px;background:{{ $bg }};min-height:2px" title="{{ $val }} posts"></div>
        </div>
        @endforeach
      </div>
      <div style="display:flex;justify-content:space-between;font:400 9.5px/1 var(--f-mono);color:var(--ink-4);letter-spacing:.06em">
        <span>{{ now()->subDays(14)->format('d M') }}</span>
        <span>{{ now()->subDays(7)->format('d M') }}</span>
        <span>{{ now()->format('d M') }}</span>
      </div>
    </div>

    {{-- Moderation queue --}}
    <div class="dash-card">
      <header style="display:flex;align-items:center">
        <span class="eyebrow">File de modération</span>
        <span style="flex:1"></span>
        <span class="chip chip-terracotta">{{ $pendingReports }} EN ATTENTE</span>
      </header>
      @forelse($reports as $report)
      @php $tone = $report->severity_color; @endphp
      <div style="padding:10px 12px;border-radius:10px;background:var(--c-{{ $tone }}-soft);border:0.5px solid;border-color:color-mix(in oklab,var(--c-{{ $tone }}) 30%,transparent);display:flex;flex-direction:column;gap:6px">
        <div style="display:flex;align-items:center;gap:8px">
          <x-ui.avatar :name="$report->post?->user?->name ?? 'Anonyme'" size="22"/>
          <span style="font:500 11.5px/1.2 var(--f-ui)">{{ $report->post?->user?->name ?? 'Anonyme' }}</span>
          <span style="flex:1"></span>
          <span style="font:500 10px/1 var(--f-mono);color:var(--ink-2)">IA · {{ number_format($report->ai_score, 2) }}</span>
        </div>
        <div style="font:400 11.5px/1.4 var(--f-ui);color:var(--ink-2)">{{ $report->reason }}</div>
        <div style="display:flex;gap:6px;margin-top:2px">
          <form action="{{ route('moderation.approve', $report) }}" method="POST" style="display:inline">@csrf
            <button class="btn" style="height:26px;font-size:11px">Approuver</button>
          </form>
          <form action="{{ route('moderation.reject', $report) }}" method="POST" style="display:inline">@csrf
            <button class="btn btn-primary" style="height:26px;font-size:11px">Examiner</button>
          </form>
          <form action="{{ route('moderation.ignore', $report) }}" method="POST" style="display:inline">@csrf
            <button class="btn btn-ghost" style="height:26px;font-size:11px">Ignorer</button>
          </form>
        </div>
      </div>
      @empty
      <div style="padding:20px;text-align:center;color:var(--ink-3);font:400 12px/1.5 var(--f-ui)">
        <x-ui.icon name="check" size="20" style="color:var(--c-atlas)"/> Aucun signalement en attente
      </div>
      @endforelse
      <a href="{{ route('moderation.index') }}" class="btn btn-ghost" style="align-self:flex-start;height:26px;font-size:11px">Voir l'historique →</a>
    </div>
  </div>

  {{-- Bottom 3-column --}}
  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">

    {{-- Active classes --}}
    <div class="dash-card">
      <span class="eyebrow">Classes les plus actives</span>
      @foreach($classes as $i => $group)
      <div style="display:grid;grid-template-columns:1fr auto auto;gap:10px;padding:9px 0;align-items:center;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }}">
        <div style="min-width:0">
          <div style="font:500 11.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $group->name }}</div>
          <div style="font:400 10.5px/1.2 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $group->teacher?->name ?? '—' }}</div>
        </div>
        <div style="width:60px;height:4px;border-radius:2px;background:var(--surface-2);position:relative">
          <div style="position:absolute;inset:0;width:{{ min($group->posts_count, 100) }}%;border-radius:2px;background:var(--c-{{ $group->color }})"></div>
        </div>
        <span style="font:500 11px/1 var(--f-mono);color:var(--ink-2);width:28px;text-align:right">{{ $group->posts_count }}</span>
      </div>
      @endforeach
    </div>

    {{-- Upcoming events --}}
    <div class="dash-card">
      <span class="eyebrow">Prochains événements</span>
      @foreach($events as $i => $event)
      <div style="display:flex;gap:10px;padding:6px 0;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }};align-items:center">
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:var(--surface-2)">
          <span style="font:600 14px/1 var(--f-ui)">{{ $event->starts_at->format('d') }}</span>
          <span style="font:500 8.5px/1 var(--f-mono);color:var(--ink-3);margin-top:2px">{{ strtoupper($event->starts_at->locale('fr')->isoFormat('MMM')) }}</span>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font:500 11.5px/1.2 var(--f-ui)">{{ $event->title }}</div>
          <div style="font:400 10.5px/1.2 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $event->location }}</div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- School pulse --}}
    <div class="dash-card" style="position:relative;overflow:hidden">
      <div style="position:absolute;bottom:-20px;right:-20px;color:var(--c-blue);opacity:.06"><x-ui.zellige-star size="140"/></div>
      <span class="eyebrow">Pouls de l'école</span>
      @foreach([['Climat général','Très positif','atlas',min($engagementPct+20,100)],['Participation classes','Soutenue','blue',$engagementPct],['Vie de club','Active','saffron',$clubPct]] as [$lbl,$val,$tone,$pct])
      <div style="display:flex;flex-direction:column;gap:5px">
        <div style="display:flex;justify-content:space-between;font:500 11px/1 var(--f-ui)">
          <span style="color:var(--ink-2)">{{ $lbl }}</span>
          <span style="color:var(--c-{{ $tone }})">{{ $val }}</span>
        </div>
        <div style="height:4px;border-radius:2px;background:var(--surface-2);overflow:hidden">
          <div style="width:{{ $pct }}%;height:100%;background:var(--c-{{ $tone }});border-radius:2px"></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
</x-layouts.app>
