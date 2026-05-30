<x-layouts.app title="Modération" subtitle="File active">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">
  <div style="display:flex;gap:4px;margin-bottom:20px">
    @foreach(['pending'=>'En attente','approved'=>'Approuvés','rejected'=>'Rejetés'] as $key=>$label)
    <a href="{{ route('moderation.index',['tab'=>$key]) }}" class="btn {{ $tab===$key?'btn-primary':'btn-ghost' }}" style="text-decoration:none;height:30px;font-size:12px">{{ $label }}</a>
    @endforeach
  </div>
  @forelse($reports as $report)
  @php $tone=$report->severity_color; @endphp
  <div style="padding:14px 16px;border-radius:var(--r-md);background:var(--c-{{ $tone }}-soft);border:0.5px solid;border-color:color-mix(in oklab,var(--c-{{ $tone }}) 30%,transparent);margin-bottom:12px;display:flex;flex-direction:column;gap:10px">
    <div style="display:flex;align-items:center;gap:10px">
      <x-ui.avatar :name="$report->post?->user?->name??'?'" size="28"/>
      <div style="flex:1">
        <div style="font:500 12.5px/1 var(--f-ui)">{{ $report->post?->user?->name }}</div>
        <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-top:3px">{{ $report->reason }}</div>
      </div>
      <span class="chip chip-{{ $tone }}" style="font-size:9px;height:18px">{{ $report->severity_label }}</span>
      <span style="font:500 10.5px/1 var(--f-mono);color:var(--ink-3)">IA · {{ number_format($report->ai_score,2) }}</span>
    </div>
    @if($report->post)
    <div style="padding:10px 12px;border-radius:8px;background:rgba(255,255,255,.7);font:400 13px/1.5 var(--f-ui);color:var(--ink-2)">{{ Str::limit($report->post->body,200) }}</div>
    @endif
    @if($tab==='pending')
    <div style="display:flex;gap:8px">
      <form action="{{ route('moderation.approve',$report) }}" method="POST">@csrf<button class="btn" style="height:28px;font-size:11px">Approuver</button></form>
      <form action="{{ route('moderation.reject',$report) }}" method="POST">@csrf<button class="btn btn-primary" style="height:28px;font-size:11px">Supprimer</button></form>
      <form action="{{ route('moderation.ignore',$report) }}" method="POST">@csrf<button class="btn btn-ghost" style="height:28px;font-size:11px">Ignorer</button></form>
    </div>
    @endif
  </div>
  @empty
  <div style="padding:60px;text-align:center;color:var(--ink-3)">Aucun signalement</div>
  @endforelse
</div>
</x-layouts.app>
