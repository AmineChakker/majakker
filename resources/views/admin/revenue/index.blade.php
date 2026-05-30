<x-layouts.admin title="Revenus" :subtitle="'MRR · '.$mrrFmt.' DH'">
<style>
.rev-card{background:var(--surface);border:0.5px solid var(--line);border-radius:var(--r-md);padding:20px;display:flex;flex-direction:column;gap:14px}
</style>
<div style="padding:28px 32px 60px;height:100%;overflow:auto" class="scroll">
  <header style="display:flex;align-items:flex-end;gap:24px;margin-bottom:24px">
    <div>
      <span class="eyebrow">Revenus · {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</span>
      <h1 class="serif" style="font:400 38px/1.05 var(--f-display);margin:8px 0 0">
        {{ $mrrFmt }} <span style="font-size:18px;color:var(--ink-3)">DH MRR</span>
      </h1>
    </div>
    <span style="font:500 12px/1 var(--f-mono);color:var(--c-atlas);margin-bottom:6px">+18% MoM</span>
  </header>

  <div style="display:grid;grid-template-columns:1fr 1.4fr;gap:16px;margin-bottom:20px">

    {{-- Donut chart --}}
    <div class="rev-card" style="align-items:center">
      <span class="eyebrow">Répartition par plan</span>
      @php
        $total = $segments->sum('amount') ?: 1;
        $R = 70; $C = 2 * M_PI * $R;
        $acc = 0;
      @endphp
      <div style="position:relative;display:flex;align-items:center;justify-content:center;width:160px;height:160px">
        <svg width="160" height="160" viewBox="0 0 160 160" style="transform:rotate(-90deg)">
          <circle cx="80" cy="80" r="{{ $R }}" fill="none" stroke="var(--surface-3)" stroke-width="14"/>
          @foreach($segments as $seg)
          @php
            $frac = $seg['amount'] / $total;
            $len  = round($frac * $C, 2);
            $dash = $len.' '.($C - $len);
            $off  = -$acc;
            $acc += $len;
          @endphp
          <circle cx="80" cy="80" r="{{ $R }}" fill="none"
            stroke="{{ $seg['color'] }}" stroke-width="14"
            stroke-dasharray="{{ $dash }}"
            stroke-dashoffset="{{ $off }}"
            stroke-linecap="butt"/>
          @endforeach
        </svg>
        <div style="position:absolute;display:flex;flex-direction:column;align-items:center;gap:2px">
          <span style="font:500 10px/1 var(--f-mono);letter-spacing:.16em;color:var(--ink-3);text-transform:uppercase">MRR</span>
          <span style="font:400 24px/1 var(--f-display);letter-spacing:-.02em">{{ number_format($total/1000,0) }}K</span>
          <span style="font:500 10px/1 var(--f-mono);color:var(--c-atlas)">+18% MoM</span>
        </div>
      </div>
      <div style="width:100%;display:flex;flex-direction:column;gap:6px">
        @foreach($segments as $seg)
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:8px;height:8px;border-radius:2px;background:{{ $seg['color'] }};flex-shrink:0"></span>
          <span style="flex:1;font:500 11.5px/1.2 var(--f-ui)">{{ $seg['label'] }}</span>
          <span style="font:500 11.5px/1 var(--f-mono);color:var(--ink-2)">{{ number_format($seg['amount']/1000,0) }}K</span>
          <span style="font:500 10px/1 var(--f-mono);color:var(--ink-3);width:32px;text-align:right">{{ $total > 0 ? round(($seg['amount']/$total)*100) : 0 }}%</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Plan breakdown table --}}
    <div class="rev-card">
      <span class="eyebrow">Détail par plan</span>
      <table style="width:100%;border-collapse:collapse">
        <thead>
          <tr style="border-bottom:0.5px solid var(--line)">
            @foreach(['Plan','Écoles','Prix/mois','MRR'] as $h)
            <th style="text-align:left;padding:6px 10px;font:500 10px/1 var(--f-mono);letter-spacing:.1em;text-transform:uppercase;color:var(--ink-3)">{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($segments as $seg)
          <tr style="border-bottom:0.5px solid var(--line)">
            <td style="padding:12px 10px">
              <span style="padding:2px 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);text-transform:uppercase;color:#fff;background:{{ $seg['color'] }}">{{ strtoupper($seg['key']) }}</span>
            </td>
            <td style="padding:12px 10px;font:500 13px/1 var(--f-mono)">{{ number_format($seg['count']) }}</td>
            <td style="padding:12px 10px;font:400 12px/1 var(--f-mono);color:var(--ink-2)">{{ number_format($seg['price']) }} DH</td>
            <td style="padding:12px 10px;font:600 13px/1 var(--f-mono)">{{ number_format($seg['amount']) }} DH</td>
          </tr>
          @endforeach
          <tr style="background:var(--surface-2)">
            <td colspan="3" style="padding:12px 10px;font:600 12px/1 var(--f-ui)">Total</td>
            <td style="padding:12px 10px;font:700 14px/1 var(--f-mono)">{{ $mrrFmt }} DH</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- Recent schools --}}
  <div class="rev-card">
    <span class="eyebrow">Nouvelles écoles ce mois-ci</span>
    <div style="display:flex;flex-direction:column">
      @foreach($recentSchools as $i => $school)
      <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-top:{{ $i===0?'0':'0.5px solid var(--line)' }}">
        <div style="width:30px;height:30px;border-radius:8px;background:var(--c-saffron-soft);color:#8A6520;display:flex;align-items:center;justify-content:center;font:600 14px/1 var(--f-display)">
          {{ $school->initial }}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $school->name }}</div>
          <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $school->city }} · {{ $school->created_at->format('d M Y') }}</div>
        </div>
        @php $planColors = ['pro'=>'#7E5BEF','school'=>'#2563EB','starter'=>'#06B6D4']; @endphp
        <span style="padding:2px 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);text-transform:uppercase;color:#fff;background:{{ $planColors[$school->plan??'starter']??'#9A9183' }}">
          {{ strtoupper($school->plan ?? 'starter') }}
        </span>
      </div>
      @endforeach
    </div>
  </div>
</div>
</x-layouts.admin>
