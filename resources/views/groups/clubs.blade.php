<x-layouts.app title="Clubs">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px">
    @forelse($allClubs as $group)
    @php $isMember = $groups->contains($group); @endphp
    <div style="display:flex;flex-direction:column;gap:10px;padding:18px;border-radius:var(--r-md);background:var(--surface);border:0.5px solid var(--line);box-shadow:var(--sh-sm)">
      <div style="font:600 14px/1.3 var(--f-ui)">{{ $group->name }}</div>
      <div style="font:500 10.5px/1 var(--f-mono);color:var(--c-{{ $group->color }})">{{ $group->member_count }} membres</div>
      @if($isMember)
      <form action="{{ route('groups.leave', $group) }}" method="POST">@csrf
        <button class="btn btn-ghost" style="font-size:11px;height:26px">Quitter</button>
      </form>
      @else
      <form action="{{ route('groups.join', $group) }}" method="POST">@csrf
        <button class="btn btn-primary" style="font-size:11px;height:26px">Rejoindre</button>
      </form>
      @endif
    </div>
    @empty
    <div style="color:var(--ink-3)">Aucun club</div>
    @endforelse
  </div>
</div>
</x-layouts.app>
