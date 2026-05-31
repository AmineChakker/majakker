<x-layouts.app title="Messages">
@php $user = auth()->user(); @endphp
<style>
.msg-shell{display:grid;grid-template-columns:300px 1fr;height:100%;overflow:hidden}
.msg-sidebar{border-right:0.5px solid var(--line);display:flex;flex-direction:column;overflow:hidden}
.msg-tab{height:36px;flex:1;border:0;background:transparent;font:500 12px/1 var(--f-ui);color:var(--ink-3);cursor:pointer;border-bottom:2px solid transparent;transition:all .14s}
.msg-tab.active{color:var(--ink);border-bottom-color:var(--c-blue)}
.contact-row{display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:inherit;cursor:pointer;transition:background .12s;border-bottom:0.5px solid var(--line)}
.contact-row:hover{background:var(--surface-2)}
.contact-row.active{background:var(--c-blue-soft)}
.role-badge-teacher{background:var(--c-atlas-soft);color:var(--c-atlas)}
.role-badge-director{background:var(--c-saffron-soft);color:#8A6520}
.role-badge-student{background:var(--c-blue-soft);color:var(--c-blue)}
</style>

<div class="msg-shell" x-data="{ tab: 'conversations', search: '' }">

  {{-- ── Left sidebar ── --}}
  <div class="msg-sidebar">

    {{-- Tabs --}}
    <div style="display:flex;border-bottom:0.5px solid var(--line);flex-shrink:0">
      <button class="msg-tab" :class="tab==='conversations'?'active':''" @click="tab='conversations'">
        Conversations
        @if($conversations->count())<span style="margin-left:5px;font:600 9.5px/1 var(--f-mono);background:var(--c-blue);color:#fff;padding:1px 5px;border-radius:999px">{{ $conversations->count() }}</span>@endif
      </button>
      <button class="msg-tab" :class="tab==='contacts'?'active':''" @click="tab='contacts'">Nouveau</button>
    </div>

    {{-- Search bar --}}
    <div style="padding:10px 14px;border-bottom:0.5px solid var(--line);flex-shrink:0">
      <div style="display:flex;align-items:center;gap:8px;height:34px;padding:0 12px;border-radius:9px;background:var(--surface-2);border:0.5px solid var(--line)">
        <x-ui.icon name="search" size="13" style="color:var(--ink-3)"/>
        <input x-model="search" type="text" placeholder="Rechercher…"
               style="flex:1;border:0;background:transparent;font:400 13px/1 var(--f-ui);color:var(--ink);outline:none"/>
      </div>
    </div>

    {{-- Conversations list --}}
    <div x-show="tab==='conversations'" class="scroll" style="flex:1;overflow:auto">
      @forelse($conversations as $conv)
      @php
        $roleClass = match($conv->role) { 'teacher'=>'role-badge-teacher','director'=>'role-badge-director',default=>'role-badge-student' };
        $roleLabel = match($conv->role) { 'teacher'=>'Prof','director'=>'Dir.','student'=>'Élève',default=>'' };
        $unread = \App\Models\Message::where('sender_id',$conv->id)->where('recipient_id',auth()->id())->whereNull('read_at')->count();
      @endphp
      <a href="{{ route('messages.show', $conv) }}" class="contact-row"
         x-show="!search || '{{ strtolower($conv->name) }}'.includes(search.toLowerCase())">
        <div style="position:relative;flex-shrink:0">
          <x-ui.avatar :name="$conv->name" size="38"/>
          @if($unread > 0)
          <span style="position:absolute;top:-2px;right:-2px;width:16px;height:16px;border-radius:999px;background:var(--c-blue);color:#fff;font:600 9px/16px var(--f-mono);text-align:center;border:1.5px solid var(--bg)">{{ $unread }}</span>
          @endif
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px">
            <span style="font:{{ $unread ? '700' : '500' }} 12.5px/1.2 var(--f-ui)">{{ $conv->name }}</span>
            <span style="padding:1px 5px;border-radius:4px;font:600 8.5px/1.4 var(--f-mono);{{ $roleClass }}">{{ $roleLabel }}</span>
          </div>
          <div style="font:400 11.5px/1.2 var(--f-ui);color:var(--ink-3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            {{ Str::limit($conv->last_message?->body, 40) }}
          </div>
        </div>
        <div style="font:400 10px/1 var(--f-mono);color:var(--ink-4);flex-shrink:0">
          {{ $conv->last_message?->created_at->format('H:i') }}
        </div>
      </a>
      @empty
      <div style="padding:40px 20px;text-align:center;color:var(--ink-3)">
        <x-ui.zellige-star size="32" color="var(--ink-4)" opacity="0.4"/>
        <div style="margin-top:10px;font:400 13px/1.5 var(--f-ui)">Aucune conversation.</div>
        <button @click="tab='contacts'" style="margin-top:8px;border:0;background:0;color:var(--c-blue);font:500 12px/1 var(--f-ui);cursor:pointer">
          Commencer une conversation →
        </button>
      </div>
      @endforelse
    </div>

    {{-- Contacts list (new message) --}}
    <div x-show="tab==='contacts'" class="scroll" style="flex:1;overflow:auto">

      @if($user->isTeacher())
        {{-- Colleagues (teachers + director) --}}
        @if($contacts['colleagues']->count())
        <div style="padding:8px 14px 4px;font:500 9.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3);background:var(--surface-2);border-bottom:0.5px solid var(--line)">
          Collègues · {{ $contacts['colleagues']->count() }}
        </div>
        @foreach($contacts['colleagues'] as $c)
        <a href="{{ route('messages.show', $c) }}" class="contact-row"
           x-show="!search || '{{ strtolower($c->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$c->name" size="34"/>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $c->name }}</div>
            <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $c->role_label }}</div>
          </div>
          <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--ink-4)" stroke-width="1.4" stroke-linecap="round"><path d="M9 6l4 4-4 4"/></svg>
        </a>
        @endforeach
        @endif

        {{-- Students per class --}}
        @foreach($contacts['classes'] as $class)
        @if($class->members->count())
        <div style="padding:8px 14px 4px;font:500 9.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--c-{{ $class->color }});background:var(--c-{{ $class->color }}-soft);border-bottom:0.5px solid var(--line);display:flex;align-items:center;gap:6px">
          <span style="width:6px;height:6px;border-radius:999px;background:var(--c-{{ $class->color }})"></span>
          {{ $class->name }} · {{ $class->members->count() }} élèves
        </div>
        @foreach($class->members as $s)
        <a href="{{ route('messages.show', $s) }}" class="contact-row"
           x-show="!search || '{{ strtolower($s->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$s->name" size="34"/>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $s->name }}</div>
            <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">Élève</div>
          </div>
          <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--ink-4)" stroke-width="1.4" stroke-linecap="round"><path d="M9 6l4 4-4 4"/></svg>
        </a>
        @endforeach
        @endif
        @endforeach

        @if($contacts['colleagues']->isEmpty() && collect($contacts['classes'])->every(fn($c) => $c->members->isEmpty()))
        <div style="padding:40px 20px;text-align:center;color:var(--ink-3);font:400 13px/1.5 var(--f-ui)">
          Aucun contact disponible.<br>Assignez-vous des classes pour contacter vos élèves.
        </div>
        @endif

      @elseif($user->isDirector())
        <div style="padding:8px 14px 4px;font:500 9.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3);background:var(--surface-2);border-bottom:0.5px solid var(--line)">
          Enseignants · {{ $contacts['teachers']->count() }}
        </div>
        @foreach($contacts['teachers'] as $t)
        <a href="{{ route('messages.show', $t) }}" class="contact-row"
           x-show="!search || '{{ strtolower($t->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$t->name" size="34"/>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $t->name }}</div>
            <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">Professeur</div>
          </div>
        </a>
        @endforeach
        <div style="padding:8px 14px 4px;font:500 9.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3);background:var(--surface-2);border-bottom:0.5px solid var(--line)">
          Élèves · {{ $contacts['students']->count() }}
        </div>
        @foreach($contacts['students'] as $s)
        <a href="{{ route('messages.show', $s) }}" class="contact-row"
           x-show="!search || '{{ strtolower($s->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$s->name" size="34"/>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $s->name }}</div>
            <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">Élève</div>
          </div>
        </a>
        @endforeach

      @else
        {{-- Students, admins: show all --}}
        @foreach($contacts['others'] as $u)
        <a href="{{ route('messages.show', $u) }}" class="contact-row"
           x-show="!search || '{{ strtolower($u->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$u->name" size="34"/>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $u->name }}</div>
            <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $u->role_label }}</div>
          </div>
        </a>
        @endforeach
      @endif
    </div>
  </div>

  {{-- ── Empty state ── --}}
  <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;color:var(--ink-3);padding:40px">
    <div style="width:64px;height:64px;border-radius:18px;background:var(--surface-2);display:flex;align-items:center;justify-content:center">
      <x-ui.icon name="msg" size="28" style="color:var(--ink-4)"/>
    </div>
    <div style="text-align:center">
      <div style="font:400 17px/1.2 var(--f-display);color:var(--ink);margin-bottom:6px">Vos messages</div>
      <div style="font:400 13px/1.5 var(--f-ui)">Sélectionnez une conversation ou démarrez-en une nouvelle.</div>
    </div>
    <button @click="tab='contacts'"
            style="display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 16px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;border:0;font:500 12.5px/1 var(--f-ui);cursor:pointer">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      Nouveau message
    </button>
  </div>
</div>
</x-layouts.app>
