<x-layouts.admin title="Rapports" subtitle="Centre de données">
<style>
.rpt-card{background:var(--surface);border:0.5px solid var(--line);border-radius:var(--r-md);padding:18px;display:flex;flex-direction:column;gap:14px}
.rpt-kpi{background:var(--surface);border:0.5px solid var(--line);border-radius:var(--r-md);padding:16px;display:flex;flex-direction:column;gap:6px}
</style>
<div style="padding:28px 32px 60px;height:100%;overflow:auto" class="scroll">
  <header style="margin-bottom:24px">
    <span class="eyebrow">Rapports · Plateforme</span>
    <h2 class="serif" style="font:400 34px/1.05 var(--f-display);margin:8px 0 0">Centre de données</h2>
  </header>

  {{-- KPI row --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
    @foreach([
      ['Écoles partenaires', $totalSchools,  'var(--c-blue)'],
      ['Élèves inscrits',    $totalStudents, 'var(--c-saffron)'],
      ['Enseignants',        $totalTeachers, 'var(--c-atlas)'],
      ['Publications',       $totalPosts,    'var(--c-terracotta)'],
    ] as [$label,$value,$color])
    <div class="rpt-kpi">
      <span class="eyebrow">{{ $label }}</span>
      <span style="font:400 32px/1 var(--f-display);letter-spacing:-.01em">{{ number_format($value) }}</span>
      <span style="width:24px;height:2px;background:{{ $color }};border-radius:1px"></span>
    </div>
    @endforeach
  </div>

  <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:16px;margin-bottom:16px">

    {{-- Weekly activity chart --}}
    <div class="rpt-card">
      <span class="eyebrow">Publications par semaine — 8 semaines</span>
      <div style="display:flex;align-items:baseline;gap:12px">
        <span style="font:400 34px/1 var(--f-display)">{{ $weeklyData->sum() }}</span>
        <span style="font:500 12px/1 var(--f-mono);color:var(--ink-3)">publications au total</span>
      </div>
      @php $maxW = max($weeklyData->max(), 1); @endphp
      <div style="display:flex;align-items:flex-end;gap:8px;height:120px">
        @foreach($weeklyData as $i => $val)
        @php
          $pct = round(($val/$maxW)*100);
          $isLast = $i === count($weeklyData)-1;
          $bg = $isLast ? 'linear-gradient(180deg,#7E5BEF,#2563EB)' : 'var(--surface-3)';
        @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%">
          <div style="flex:1;width:100%;display:flex;align-items:flex-end">
            <div style="width:100%;height:{{ max($pct,2) }}%;border-radius:4px 4px 1px 1px;background:{{ $bg }};min-height:2px"></div>
          </div>
          <span style="font:400 9px/1 var(--f-mono);color:var(--ink-4);letter-spacing:.02em">{{ $weekLabels[$i] }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Moderation summary --}}
    <div class="rpt-card">
      <span class="eyebrow">Résumé de modération</span>
      @php
        $modItems = [
          ['pending',  'En attente', 'var(--c-saffron)',    'chip-saffron'],
          ['approved', 'Approuvés',  'var(--c-atlas)',      'chip-atlas'],
          ['rejected', 'Rejetés',    'var(--c-terracotta)', 'chip-terracotta'],
          ['ignored',  'Ignorés',    'var(--ink-3)',        ''],
        ];
        $modTotal = $modSummary->sum() ?: 1;
      @endphp
      @foreach($modItems as [$key,$label,$color,$chipClass])
      @php $count = $modSummary[$key] ?? 0; @endphp
      <div style="display:flex;align-items:center;gap:10px">
        <span class="chip {{ $chipClass }}" style="font-size:9.5px;width:80px;text-align:center">{{ $label }}</span>
        <div style="flex:1;height:4px;background:var(--surface-3);border-radius:2px">
          <div style="height:100%;width:{{ round(($count/$modTotal)*100) }}%;background:{{ $color }};border-radius:2px"></div>
        </div>
        <span style="font:600 12px/1 var(--f-mono);color:var(--ink-2);width:36px;text-align:right">{{ $count }}</span>
      </div>
      @endforeach
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

    {{-- Top schools by posts --}}
    <div class="rpt-card">
      <span class="eyebrow">Écoles les plus actives</span>
      <div style="display:flex;flex-direction:column">
        @foreach($topSchoolsByPosts as $i => $school)
        @php $maxP = $topSchoolsByPosts->first()->posts_count ?: 1; @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }}">
          <span style="font:500 11px/1 var(--f-mono);color:var(--ink-4);width:18px;text-align:center">{{ $i+1 }}</span>
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:baseline;gap:6px">
              <span style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $school->name }}</span>
              <span style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3)">{{ $school->city }}</span>
            </div>
            <div style="height:3px;background:var(--surface-3);border-radius:2px;margin-top:4px">
              <div style="height:100%;width:{{ round(($school->posts_count/$maxP)*100) }}%;background:linear-gradient(90deg,#7E5BEF,#2563EB);border-radius:2px"></div>
            </div>
          </div>
          <span style="font:600 12px/1 var(--f-mono);color:var(--ink-2)">{{ number_format($school->posts_count) }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Top schools by users --}}
    <div class="rpt-card">
      <span class="eyebrow">Écoles par nombre d'utilisateurs</span>
      <div style="display:flex;flex-direction:column">
        @foreach($topSchoolsByUsers as $i => $school)
        @php $maxU = $topSchoolsByUsers->first()->users_count ?: 1; @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }}">
          <span style="font:500 11px/1 var(--f-mono);color:var(--ink-4);width:18px;text-align:center">{{ $i+1 }}</span>
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:baseline;gap:6px">
              <span style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $school->name }}</span>
              <span style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3)">{{ $school->city }}</span>
            </div>
            <div style="height:3px;background:var(--surface-3);border-radius:2px;margin-top:4px">
              <div style="height:100%;width:{{ round(($school->users_count/$maxU)*100) }}%;background:var(--c-atlas);border-radius:2px"></div>
            </div>
          </div>
          <span style="font:600 12px/1 var(--f-mono);color:var(--ink-2)">{{ number_format($school->users_count) }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
</x-layouts.admin>
