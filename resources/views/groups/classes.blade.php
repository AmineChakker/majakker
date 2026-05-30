<x-layouts.app title="Mes classes">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px">
    @forelse($groups as $group)
    <a href="{{ route('groups.show', $group) }}" style="display:flex;flex-direction:column;gap:10px;padding:18px;border-radius:var(--r-md);background:var(--surface);border:0.5px solid var(--line);box-shadow:var(--sh-sm);text-decoration:none;color:inherit;border-left:3px solid var(--c-{{ $group->color }})">
      <div style="font:600 14px/1.3 var(--f-ui)">{{ $group->name }}</div>
      @if($group->teacher)<div style="font:400 12px/1 var(--f-ui);color:var(--ink-3)">{{ $group->teacher->name }}</div>@endif
      <div style="font:500 10.5px/1 var(--f-mono);color:var(--c-{{ $group->color }})">{{ $group->member_count }} membres</div>
    </a>
    @empty
    <div style="color:var(--ink-3)">Aucune classe</div>
    @endforelse
  </div>
</div>
</x-layouts.app>
