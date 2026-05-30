<x-layouts.app title="Analyse" subtitle="École">
<style>
.ana-card{background:var(--surface);border:0.5px solid var(--line);border-radius:var(--r-md);padding:18px;display:flex;flex-direction:column;gap:14px}
</style>
<div style="padding:28px 32px 60px;height:100%;overflow:auto" class="scroll">
  <header style="margin-bottom:24px">
    <span class="eyebrow">Analyse · 30 derniers jours</span>
    <h2 class="serif" style="font:400 34px/1.05 var(--f-display);margin:8px 0 0">Activité de l'école</h2>
  </header>

  {{-- Activity chart --}}
  <div class="ana-card" style="margin-bottom:16px">
    <header style="display:flex;align-items:baseline;gap:12px">
      <span class="eyebrow">Publications par jour</span>
      <span style="flex:1"></span>
      <span style="font:500 11px/1 var(--f-mono);color:var(--c-atlas)">30 jours</span>
    </header>
    <div style="display:flex;align-items:baseline;gap:12px">
      <span style="font:400 38px/1 var(--f-display)">{{ $chartData->sum() }}</span>
      <span style="font:500 12px/1 var(--f-mono);color:var(--ink-3)">publications au total</span>
    </div>
    @php $maxVal = max($chartData->max(), 1); @endphp
    <div style="display:flex;align-items:flex-end;gap:4px;height:100px">
      @foreach($chartData as $i => $val)
      @php
        $pct = round(($val/$maxVal)*100);
        $isLast = $i === count($chartData)-1;
        $isRecent = $i >= count($chartData)-5;
        $bg = $isLast ? 'var(--c-blue)' : ($isRecent ? 'rgba(61,90,254,.5)' : 'var(--surface-3)');
      @endphp
      <div style="flex:1;height:{{ max($pct,2) }}%;border-radius:3px 3px 1px 1px;background:{{ $bg }};min-height:2px" title="{{ $val }} publications"></div>
      @endforeach
    </div>
    <div style="display:flex;justify-content:space-between;font:400 9.5px/1 var(--f-mono);color:var(--ink-4)">
      <span>{{ now()->subDays(29)->format('d M') }}</span>
      <span>{{ now()->subDays(14)->format('d M') }}</span>
      <span>{{ now()->format('d M') }}</span>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

    {{-- Top users --}}
    <div class="ana-card">
      <span class="eyebrow">Utilisateurs les plus actifs</span>
      <div style="display:flex;flex-direction:column">
        @foreach($topUsers as $i => $u)
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }}">
          <span style="font:500 11px/1 var(--f-mono);color:var(--ink-4);width:16px;text-align:center">{{ $i+1 }}</span>
          <x-ui.avatar :name="$u->name" size="28"/>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $u->name }}</div>
            <div style="font:400 10.5px/1 var(--f-mono);color:var(--ink-3);margin-top:2px">{{ $u->role_label }}</div>
          </div>
          <span style="font:600 13px/1 var(--f-mono);color:var(--ink-2)">{{ $u->posts_count }}</span>
        </div>
        @endforeach
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">

      {{-- Top hashtags --}}
      <div class="ana-card">
        <span class="eyebrow">Hashtags populaires</span>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          @foreach($topHashtags as $tag)
          <span class="chip" style="font-size:11px">#{{ $tag->name }} <span style="color:var(--ink-4);margin-left:4px">{{ $tag->posts_count }}</span></span>
          @endforeach
        </div>
      </div>

      {{-- Top groups --}}
      <div class="ana-card">
        <span class="eyebrow">Groupes les plus actifs</span>
        <div style="display:flex;flex-direction:column">
          @foreach($topGroups as $i => $g)
          @php $maxPosts = $topGroups->first()->posts_count ?: 1; @endphp
          <div style="display:flex;flex-direction:column;gap:4px;padding:8px 0;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }}">
            <div style="display:flex;align-items:center;gap:8px">
              <span style="width:8px;height:8px;border-radius:2px;background:var(--c-{{ $g->color }});flex-shrink:0"></span>
              <span style="font:500 12px/1.2 var(--f-ui);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $g->name }}</span>
              <span style="font:600 11.5px/1 var(--f-mono)">{{ $g->posts_count }}</span>
            </div>
            <div style="height:3px;background:var(--surface-3);border-radius:2px">
              <div style="height:100%;width:{{ round(($g->posts_count/$maxPosts)*100) }}%;background:var(--c-{{ $g->color }});border-radius:2px"></div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</div>
</x-layouts.app>
