<x-layouts.app title="Notifications">
<div style="max-width:720px;margin:0 auto;padding:28px 24px" class="scroll" style="height:100%;overflow:auto">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <h2 class="serif" style="font:400 28px/1 var(--f-display)">Notifications</h2>
    <form action="{{ route('notifications.read-all') }}" method="POST">
      @csrf <button class="btn btn-ghost" style="font-size:12px">Tout marquer lu</button>
    </form>
  </div>
  @forelse($notifications as $n)
  @php $data = is_string($n->data) ? json_decode($n->data,true) : $n->data; @endphp
  <div style="display:flex;gap:14px;padding:14px 0;border-bottom:0.5px solid var(--line);background:{{ $n->read_at ? 'transparent' : 'var(--c-blue-soft)' }};border-radius:{{ $n->read_at ? '0':'8px' }};padding:{{ $n->read_at ? '14px 0':'14px 12px' }}">
    <x-ui.avatar :name="$data['actor'] ?? '?'" size="36"/>
    <div style="flex:1;min-width:0">
      <div style="font:500 13px/1.4 var(--f-ui)">{{ $data['message'] ?? 'Nouvelle notification' }}</div>
      <div style="font:400 11px/1 var(--f-mono);color:var(--ink-3);margin-top:6px">{{ $n->created_at->diffForHumans() }}</div>
    </div>
    @if(!$n->read_at)
    <span style="width:8px;height:8px;border-radius:999px;background:var(--c-blue);flex-shrink:0;margin-top:6px"></span>
    @endif
  </div>
  @empty
  <div style="padding:60px;text-align:center;color:var(--ink-3)">
    <x-ui.zellige-star size="40" color="var(--ink-4)" opacity="0.4"/>
    <div style="margin-top:12px">Aucune notification</div>
  </div>
  @endforelse
</div>
</x-layouts.app>
