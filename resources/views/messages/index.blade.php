<x-layouts.app title="Messages">
<div style="display:grid;grid-template-columns:280px 1fr;height:100%;overflow:hidden">
  <aside style="border-right:0.5px solid var(--line);overflow:auto" class="scroll">
    <div style="padding:16px;border-bottom:0.5px solid var(--line)"><span class="eyebrow">Conversations</span></div>
    @forelse($conversations as $conv)
    <a href="{{ route('messages.show', $conv) }}" style="display:flex;gap:10px;padding:12px 16px;border-bottom:0.5px solid var(--line);text-decoration:none;color:inherit;transition:background .14s" @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
      <x-ui.avatar :name="$conv->name" size="36"/>
      <div style="flex:1;min-width:0">
        <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $conv->name }}</div>
        <div style="font:400 11.5px/1.2 var(--f-ui);color:var(--ink-3);margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $conv->last_message?->body }}</div>
      </div>
      <div style="font:400 10px/1 var(--f-mono);color:var(--ink-4)">{{ $conv->last_message?->created_at->format('H:i') }}</div>
    </a>
    @empty
    <div style="padding:40px;text-align:center;color:var(--ink-3);font-size:13px">Aucune conversation</div>
    @endforelse
  </aside>
  <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--ink-3)">
    <x-ui.icon name="msg" size="40" style="color:var(--ink-4)"/>
    <div style="margin-top:12px;font:400 14px/1.5 var(--f-ui)">Sélectionnez une conversation</div>
  </div>
</div>
</x-layouts.app>
