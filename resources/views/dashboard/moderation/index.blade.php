<x-layouts.app title="Modération" subtitle="File active">
@include('dashboard._mgmt_styles')

<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
      <span class="dir-eyebrow">Modération du contenu</span>
      <h2 style="font:400 28px/1.1 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        Signalements
      </h2>
    </div>
    <div style="display:flex;gap:5px">
      @foreach(['pending'=>'En attente','approved'=>'Approuvés','rejected'=>'Rejetés','ignored'=>'Ignorés'] as $key=>$label)
      <a href="{{ route('moderation.index',['tab'=>$key]) }}"
         class="dir-btn-{{ $tab===$key?'primary':'ghost' }}"
         style="text-decoration:none;height:32px;font-size:11.5px">
        {{ $label }}
        @if($tab===$key)
        <span style="padding:1px 6px;border-radius:999px;background:rgba(255,255,255,.22);font:600 9px/1.4 var(--f-mono)">
          {{ $reports->total() }}
        </span>
        @endif
      </a>
      @endforeach
    </div>
  </div>

  @forelse($reports as $report)
  @php
    $sev      = $report->severity ?? 'low';
    $sevColor = match($sev) { 'high'=>'#B91C1C','medium'=>'#B45309',default=>'#047857' };
    $sevBg    = match($sev) { 'high'=>'rgba(220,38,38,.08)','medium'=>'rgba(245,158,11,.10)',default=>'rgba(16,185,129,.08)' };
    $sevBd    = match($sev) { 'high'=>'rgba(220,38,38,.2)','medium'=>'rgba(245,158,11,.2)',default=>'rgba(16,185,129,.2)' };
    $sevLabel = match($sev) { 'high'=>'HAUT','medium'=>'MOY.',default=>'BAS' };
  @endphp

  <div style="background:var(--surface);border:0.5px solid var(--line);border-radius:14px;
              margin-bottom:12px;overflow:hidden;transition:box-shadow .2s"
       onmouseenter="this.style.boxShadow='0 8px 28px -12px rgba(20,21,43,.12)'"
       onmouseleave="this.style.boxShadow='none'">

    {{-- Report header --}}
    <div style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:0.5px solid var(--line)">

      {{-- Severity badge --}}
      <span style="display:inline-flex;align-items:center;gap:5px;height:22px;padding:0 10px;border-radius:999px;
                   background:{{ $sevBg }};border:0.5px solid {{ $sevBd }};color:{{ $sevColor }};
                   font:600 9.5px/1 var(--f-mono);letter-spacing:.08em;flex-shrink:0">
        <span style="width:5px;height:5px;border-radius:999px;background:{{ $sevColor }}"></span>
        {{ $sevLabel }}
      </span>

      {{-- Reporter --}}
      @if($report->reporter)
      <div style="display:flex;align-items:center;gap:7px">
        <x-ui.avatar :name="$report->reporter->name" size="24"/>
        <div>
          <span style="font:500 12px/1 var(--f-ui);color:var(--ink)">{{ $report->reporter->name }}</span>
          <span style="font:400 11px/1 var(--f-ui);color:var(--ink-3)"> a signalé</span>
        </div>
      </div>
      @else
      <span style="font:400 11.5px/1 var(--f-ui);color:var(--ink-3)">Signalement automatique (IA)</span>
      @endif

      <span style="flex:1"></span>

      {{-- Timestamp --}}
      <span style="font:400 10.5px/1 var(--f-mono);color:var(--ink-4)">
        {{ $report->created_at->locale('fr')->isoFormat('D MMM · HH:mm') }}
      </span>

      @if($report->ai_score)
      <span style="padding:2px 8px;border-radius:5px;background:var(--surface-2);border:0.5px solid var(--line-2);font:500 9.5px/1.4 var(--f-mono);color:var(--ink-3)">
        IA {{ number_format($report->ai_score,2) }}
      </span>
      @endif
    </div>

    {{-- Reason --}}
    <div style="padding:12px 18px;border-bottom:0.5px solid var(--line);
                display:flex;align-items:flex-start;gap:8px;background:{{ $sevBg }}">
      <x-ui.icon name="flag" size="13" style="color:{{ $sevColor }};flex-shrink:0;margin-top:1px"/>
      <span style="font:500 13px/1.4 var(--f-ui);color:{{ $sevColor }}">{{ $report->reason }}</span>
    </div>

    {{-- Post content --}}
    @if($report->post)
    <div style="padding:14px 18px;border-bottom:0.5px solid var(--line)">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
        <x-ui.avatar :name="$report->post->user?->name ?? '?'" size="30"/>
        <div>
          <div style="font:600 13px/1.2 var(--f-ui);color:var(--ink)">{{ $report->post->user?->name ?? 'Anonyme' }}</div>
          <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">
            {{ $report->post->created_at->locale('fr')->isoFormat('D MMM YYYY · HH:mm') }}
            @if($report->post->group) · {{ $report->post->group->name }}@endif
          </div>
        </div>
      </div>
      @if($report->post->title)
      <div style="font:700 14px/1.35 var(--f-ui);color:var(--ink);margin-bottom:6px">{{ $report->post->title }}</div>
      @endif
      <div style="font:400 13.5px/1.6 var(--f-ui);color:var(--ink-2);white-space:pre-wrap">{{ \Illuminate\Support\Str::limit($report->post->body, 400) }}</div>
    </div>
    @endif

    {{-- Actions --}}
    @if($tab==='pending')
    <div style="display:flex;align-items:center;gap:8px;padding:12px 18px">
      <form action="{{ route('moderation.approve',$report) }}" method="POST">@csrf
        <button class="dir-btn-ghost" style="height:30px;font-size:11.5px;gap:6px">
          <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
          Approuver (contenu ok)
        </button>
      </form>
      <form action="{{ route('moderation.reject',$report) }}" method="POST"
            onsubmit="return confirm('Supprimer définitivement cette publication ?')">@csrf
        <button style="display:inline-flex;align-items:center;gap:6px;height:30px;padding:0 14px;border-radius:8px;border:0.5px solid rgba(220,38,38,.25);background:rgba(220,38,38,.07);font:500 11.5px/1 var(--f-ui);color:#B91C1C;cursor:pointer">
          <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 6h12M8 6V4h4v2M7 6v10a1 1 0 001 1h4a1 1 0 001-1V6"/></svg>
          Supprimer la publication
        </button>
      </form>
      <form action="{{ route('moderation.ignore',$report) }}" method="POST" style="margin-left:auto">@csrf
        <button class="dir-btn-ghost" style="height:30px;font-size:11.5px;color:var(--ink-4)">Ignorer</button>
      </form>
    </div>
    @else
    <div style="padding:10px 18px;display:flex;align-items:center;gap:8px">
      <span style="font:400 11.5px/1 var(--f-ui);color:var(--ink-3)">
        @if($tab==='approved') Contenu approuvé — aucune action requise.
        @elseif($tab==='rejected') Publication supprimée.
        @else Signalement ignoré.
        @endif
      </span>
      @if($report->reviewer)
      <span style="font:400 11px/1 var(--f-ui);color:var(--ink-4)">· par {{ $report->reviewer->name }}</span>
      @endif
      @if($report->reviewed_at)
      <span style="font:400 10.5px/1 var(--f-mono);color:var(--ink-4)">· {{ $report->reviewed_at->locale('fr')->isoFormat('D MMM') }}</span>
      @endif
    </div>
    @endif
  </div>
  @empty
  <div style="padding:80px;text-align:center;color:var(--ink-3)">
    <div style="display:flex;justify-content:center;margin-bottom:16px">
      <svg width="36" height="36" viewBox="0 0 20 20" fill="none" stroke="var(--ink-4)" stroke-width="1.2" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
    </div>
    <div style="font:400 16px/1.5 var(--f-display);color:var(--ink-2);margin-bottom:6px">Aucun signalement</div>
    <div style="font:400 13px/1.5 var(--f-ui)">Cette file est vide.</div>
  </div>
  @endforelse

  {{-- Pagination --}}
  @if($reports->hasPages())
  <div style="padding:16px 0;display:flex;gap:6px;justify-content:center">
    {{ $reports->links() }}
  </div>
  @endif
</div>
</x-layouts.app>
