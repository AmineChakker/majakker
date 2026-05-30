<x-layouts.app :title="$group->name" :subtitle="$group->kind==='class'?'Classe':'Club'">
<div style="height:100%;display:flex;flex-direction:column">
  <div style="padding:18px 24px;background:var(--surface);border-bottom:0.5px solid var(--line);display:flex;align-items:center;gap:16px">
    <div style="width:6px;height:36px;border-radius:3px;background:var(--c-{{ $group->color }})"></div>
    <div>
      <h2 style="font:600 18px/1.2 var(--f-ui);margin:0">{{ $group->name }}</h2>
      <div style="font:400 12px/1 var(--f-ui);color:var(--ink-3);margin-top:4px">
        {{ $group->member_count }} membres{{ $group->teacher ? ' · '.$group->teacher->name : '' }}
      </div>
    </div>
    <span style="flex:1"></span>
    @if($isMember)
    <form action="{{ route('groups.leave', $group) }}" method="POST">@csrf<button class="btn btn-ghost">Quitter</button></form>
    @else
    <form action="{{ route('groups.join', $group) }}" method="POST">@csrf<button class="btn btn-primary">Rejoindre</button></form>
    @endif
  </div>
  <div style="flex:1;overflow:auto" class="scroll">
    @if($isMember)<x-feed.composer/>@endif
    @forelse($posts as $post)
      <x-feed.post-card :post="$post"/>
    @empty
      <div style="padding:60px;text-align:center;color:var(--ink-3)">Aucune publication dans ce groupe</div>
    @endforelse
  </div>
</div>
</x-layouts.app>
