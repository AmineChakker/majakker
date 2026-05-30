<x-layouts.admin title="Modération IA" subtitle="File active">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <h2 class="serif" style="font:400 32px/1 var(--f-display)">Modération IA</h2>
    <span class="chip chip-terracotta" style="font-size:10px">{{ $reports->total() }} signalement(s)</span>
  </div>

  <div style="display:flex;gap:4px;margin-bottom:20px">
    @foreach(['pending'=>'En attente','approved'=>'Approuvés','rejected'=>'Rejetés','ignored'=>'Ignorés'] as $key=>$label)
    <a href="{{ route('admin.moderation', ['tab'=>$key]) }}"
       class="btn {{ $tab===$key?'btn-primary':'btn-ghost' }}"
       style="text-decoration:none;height:30px;font-size:12px">{{ $label }}</a>
    @endforeach
  </div>

  @forelse($reports as $report)
  @php $tone = $report->severity_color ?? 'saffron'; @endphp
  <div style="padding:14px 16px;border-radius:var(--r-md);background:var(--c-{{ $tone }}-soft);border:0.5px solid;border-color:color-mix(in oklab,var(--c-{{ $tone }}) 30%,transparent);margin-bottom:12px;display:flex;flex-direction:column;gap:10px">
    <div style="display:flex;align-items:center;gap:10px">
      <x-ui.avatar :name="$report->post?->user?->name??'?'" size="28"/>
      <div style="flex:1;min-width:0">
        <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $report->post?->user?->name ?? 'Utilisateur supprimé' }}</div>
        @if($report->post?->user?->school)
        <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $report->post->user->school->name }}</div>
        @endif
      </div>
      <span class="chip chip-{{ $tone }}" style="font-size:9px;height:18px">{{ $report->severity_label ?? ucfirst($tone) }}</span>
      @if($report->ai_score)
      <span style="font:500 10.5px/1 var(--f-mono);color:var(--ink-3)">IA · {{ number_format($report->ai_score,2) }}</span>
      @endif
      <span style="font:400 10px/1 var(--f-mono);color:var(--ink-4)">{{ $report->created_at->diffForHumans() }}</span>
    </div>
    @if($report->reason)
    <div style="font:400 12px/1.4 var(--f-ui);color:var(--ink-2)">{{ $report->reason }}</div>
    @endif
    @if($report->post)
    <div style="padding:10px 12px;border-radius:8px;background:rgba(255,255,255,.7);font:400 13px/1.5 var(--f-ui);color:var(--ink-2)">{{ Str::limit($report->post->body, 250) }}</div>
    @endif
    @if($tab==='pending')
    <div style="display:flex;gap:8px">
      <form action="{{ route('moderation.approve', $report) }}" method="POST">@csrf<button class="btn" style="height:28px;font-size:11px">Approuver</button></form>
      <form action="{{ route('moderation.reject', $report) }}" method="POST">@csrf<button class="btn btn-primary" style="height:28px;font-size:11px;background:var(--c-terracotta)">Supprimer</button></form>
      <form action="{{ route('moderation.ignore', $report) }}" method="POST">@csrf<button class="btn btn-ghost" style="height:28px;font-size:11px">Ignorer</button></form>
    </div>
    @endif
  </div>
  @empty
  <div style="padding:80px;text-align:center;color:var(--ink-3)">
    <x-ui.zellige-star size="40" color="var(--ink-4)" opacity="0.4"/>
    <div style="margin-top:12px">Aucun signalement dans cette catégorie</div>
  </div>
  @endforelse
  <div style="margin-top:20px">{{ $reports->links() }}</div>
</div>
</x-layouts.admin>
