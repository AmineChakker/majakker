<x-layouts.app :title="'Messages'" :subtitle="$user->name">
@php $me = auth()->user(); @endphp
<style>
.msg-shell{display:grid;grid-template-columns:300px 1fr;height:100%;overflow:hidden}
.msg-sidebar{border-right:0.5px solid var(--line);display:flex;flex-direction:column;overflow:hidden}
.msg-tab{height:36px;flex:1;border:0;background:transparent;font:500 12px/1 var(--f-ui);color:var(--ink-3);cursor:pointer;border-bottom:2px solid transparent;transition:all .14s}
.msg-tab.active{color:var(--ink);border-bottom-color:var(--c-blue)}
.contact-row{display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:inherit;cursor:pointer;transition:background .12s;border-bottom:0.5px solid var(--line)}
.contact-row:hover{background:var(--surface-2)}
.contact-row.active-conv{background:var(--c-blue-soft)}
</style>

<div class="msg-shell" x-data="{ tab: 'conversations', search: '' }">

  {{-- ── Left sidebar (same as index) ── --}}
  <div class="msg-sidebar">
    <div style="display:flex;border-bottom:0.5px solid var(--line);flex-shrink:0">
      <button class="msg-tab" :class="tab==='conversations'?'active':''" @click="tab='conversations'">Conversations</button>
      <button class="msg-tab" :class="tab==='contacts'?'active':''" @click="tab='contacts'">Nouveau</button>
    </div>
    <div style="padding:10px 14px;border-bottom:0.5px solid var(--line);flex-shrink:0">
      <div style="display:flex;align-items:center;gap:8px;height:34px;padding:0 12px;border-radius:9px;background:var(--surface-2);border:0.5px solid var(--line)">
        <x-ui.icon name="search" size="13" style="color:var(--ink-3)"/>
        <input x-model="search" type="text" placeholder="Rechercher…"
               style="flex:1;border:0;background:transparent;font:400 13px/1 var(--f-ui);color:var(--ink);outline:none"/>
      </div>
    </div>

    {{-- Conversations --}}
    <div x-show="tab==='conversations'" class="scroll" style="flex:1;overflow:auto">
      @php
        $conversations = app(App\Http\Controllers\MessageController::class)->getAllowedContacts($me);
        // Get actual conversation list
        $convUsers = \App\Models\User::whereIn('id', function($q) use($me) {
          $q->select(\Illuminate\Support\Facades\DB::raw('CASE WHEN sender_id = '.$me->id.' THEN recipient_id ELSE sender_id END'))
            ->from('messages')
            ->where(fn($q2)=>$q2->where('sender_id',$me->id)->orWhere('recipient_id',$me->id));
        })->get()->map(function($u) use($me){
          $u->last_message = \App\Models\Message::where(fn($q)=>$q->where('sender_id',$me->id)->where('recipient_id',$u->id))
            ->orWhere(fn($q)=>$q->where('sender_id',$u->id)->where('recipient_id',$me->id))
            ->latest()->first();
          return $u;
        })->sortByDesc(fn($u)=>$u->last_message?->created_at)->values();
      @endphp
      @forelse($convUsers as $conv)
      @php $unread = \App\Models\Message::where('sender_id',$conv->id)->where('recipient_id',$me->id)->whereNull('read_at')->count(); @endphp
      <a href="{{ route('messages.show', $conv) }}"
         class="contact-row {{ $conv->id === $user->id ? 'active-conv' : '' }}"
         x-show="!search || '{{ strtolower($conv->name) }}'.includes(search.toLowerCase())">
        <div style="position:relative;flex-shrink:0">
          <x-ui.avatar :name="$conv->name" size="38"/>
          @if($unread)<span style="position:absolute;top:-2px;right:-2px;width:16px;height:16px;border-radius:999px;background:var(--c-blue);color:#fff;font:600 9px/16px var(--f-mono);text-align:center;border:1.5px solid var(--bg)">{{ $unread }}</span>@endif
        </div>
        <div style="flex:1;min-width:0">
          <div style="font:{{ $unread ? '700' : '500' }} 12.5px/1.2 var(--f-ui)">{{ $conv->name }}</div>
          <div style="font:400 11.5px/1.2 var(--f-ui);color:var(--ink-3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Str::limit($conv->last_message?->body,40) }}</div>
        </div>
        <div style="font:400 10px/1 var(--f-mono);color:var(--ink-4)">{{ $conv->last_message?->created_at->format('H:i') }}</div>
      </a>
      @empty
      <div style="padding:30px;text-align:center;color:var(--ink-3);font:400 12px/1.5 var(--f-ui)">Aucune conversation</div>
      @endforelse
    </div>

    {{-- Contacts (new message) --}}
    <div x-show="tab==='contacts'" class="scroll" style="flex:1;overflow:auto">
      @if($me->isTeacher())
        @if(isset($contacts['colleagues']) && $contacts['colleagues']->count())
        <div style="padding:8px 14px 4px;font:500 9.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3);background:var(--surface-2);border-bottom:0.5px solid var(--line)">
          Collègues
        </div>
        @foreach($contacts['colleagues'] as $c)
        <a href="{{ route('messages.show', $c) }}" class="contact-row {{ $c->id===$user->id?'active-conv':'' }}"
           x-show="!search || '{{ strtolower($c->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$c->name" size="34"/>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui)">{{ $c->name }}</div>
            <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">{{ $c->role_label }}</div>
          </div>
        </a>
        @endforeach
        @endif
        @foreach($contacts['classes'] as $class)
        @if($class->members->count())
        <div style="padding:8px 14px 4px;font:500 9.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--c-{{ $class->color }});background:var(--c-{{ $class->color }}-soft);border-bottom:0.5px solid var(--line)">
          {{ $class->name }}
        </div>
        @foreach($class->members as $s)
        <a href="{{ route('messages.show', $s) }}" class="contact-row {{ $s->id===$user->id?'active-conv':'' }}"
           x-show="!search || '{{ strtolower($s->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$s->name" size="34"/>
          <div style="flex:1;min-width:0"><div style="font:500 12.5px/1.2 var(--f-ui)">{{ $s->name }}</div></div>
        </a>
        @endforeach
        @endif
        @endforeach
      @elseif($me->isDirector())
        @foreach($contacts['teachers'] as $t)
        <a href="{{ route('messages.show', $t) }}" class="contact-row {{ $t->id===$user->id?'active-conv':'' }}"
           x-show="!search || '{{ strtolower($t->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$t->name" size="34"/>
          <div style="flex:1;min-width:0"><div style="font:500 12.5px/1.2 var(--f-ui)">{{ $t->name }}</div><div style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">Professeur</div></div>
        </a>
        @endforeach
        @foreach($contacts['students'] as $s)
        <a href="{{ route('messages.show', $s) }}" class="contact-row {{ $s->id===$user->id?'active-conv':'' }}"
           x-show="!search || '{{ strtolower($s->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$s->name" size="34"/>
          <div style="flex:1;min-width:0"><div style="font:500 12.5px/1.2 var(--f-ui)">{{ $s->name }}</div><div style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">Élève</div></div>
        </a>
        @endforeach
      @else
        @foreach($contacts['others'] as $u2)
        <a href="{{ route('messages.show', $u2) }}" class="contact-row {{ $u2->id===$user->id?'active-conv':'' }}"
           x-show="!search || '{{ strtolower($u2->name) }}'.includes(search.toLowerCase())">
          <x-ui.avatar :name="$u2->name" size="34"/>
          <div style="flex:1;min-width:0"><div style="font:500 12.5px/1.2 var(--f-ui)">{{ $u2->name }}</div><div style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">{{ $u2->role_label }}</div></div>
        </a>
        @endforeach
      @endif
    </div>
  </div>

  {{-- ── Conversation thread ── --}}
  <div style="display:flex;flex-direction:column;height:100%;overflow:hidden">

    {{-- Thread header --}}
    <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:0.5px solid var(--line);background:var(--surface);flex-shrink:0">
      <x-ui.avatar :name="$user->name" size="38"/>
      <div>
        <div style="font:600 14px/1.2 var(--f-ui)">{{ $user->name }}</div>
        <div style="display:flex;align-items:center;gap:6px;margin-top:3px">
          @php
            $roleChip = match($user->role) { 'teacher'=>'chip-atlas','director'=>'chip-saffron',default=>'chip-blue' };
            $roleLabel = match($user->role) { 'teacher'=>'Professeur','director'=>'Directeur','student'=>'Élève',default=>$user->role_label };
          @endphp
          <span class="chip {{ $roleChip }}" style="font-size:9.5px">{{ $roleLabel }}</span>
          @if($user->school)<span style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">{{ $user->school->name }}</span>@endif
        </div>
      </div>
    </div>

    {{-- Messages --}}
    <div class="scroll" style="flex:1;overflow:auto;padding:16px 20px;display:flex;flex-direction:column;gap:10px" id="msg-thread">
      @forelse($messages as $msg)
      @php $mine = $msg->sender_id === $me->id; @endphp
      <div style="display:flex;justify-content:{{ $mine?'flex-end':'flex-start' }};gap:8px;align-items:flex-end">
        @if(!$mine)<x-ui.avatar :name="$user->name" size="26"/>@endif
        <div style="max-width:62%">
          <div style="padding:10px 14px;border-radius:{{ $mine?'14px 14px 4px 14px':'14px 14px 14px 4px' }};
                      background:{{ $mine?'linear-gradient(135deg,#7E5BEF,#2563EB)':'var(--surface)' }};
                      color:{{ $mine?'#fff':'var(--ink)' }};
                      border:0.5px solid {{ $mine?'transparent':'var(--line)' }};
                      font:400 13.5px/1.55 var(--f-ui);
                      box-shadow:{{ $mine?'0 4px 14px -4px rgba(94,57,224,.35)':'var(--sh-sm)' }}">
            {{ $msg->body }}
          </div>
          <div style="font:400 10px/1 var(--f-mono);color:var(--ink-4);margin-top:4px;{{ $mine?'text-align:right':'' }}">
            {{ $msg->created_at->format('H:i') }}
            @if($mine && $msg->read_at)
            <span style="color:var(--c-atlas)">· Lu</span>
            @endif
          </div>
        </div>
        @if($mine)<x-ui.avatar :name="$me->name" size="26"/>@endif
      </div>
      @empty
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:var(--ink-3);padding:40px">
        <x-ui.zellige-star size="36" color="var(--ink-4)" opacity="0.4"/>
        <div style="font:400 13px/1.5 var(--f-ui);text-align:center">
          Commencez la conversation avec {{ $user->name }}
        </div>
      </div>
      @endforelse
    </div>

    {{-- Input --}}
    <div style="padding:12px 20px;border-top:0.5px solid var(--line);background:var(--surface);flex-shrink:0">
      <form action="{{ route('messages.send', $user) }}" method="POST"
            style="display:flex;gap:10px;align-items:center">
        @csrf
        <x-ui.avatar :name="$me->name" size="32"/>
        <div style="flex:1;display:flex;align-items:center;gap:10px;background:var(--surface-2);border-radius:24px;border:0.5px solid var(--line);padding:6px 6px 6px 16px">
          <input name="body" placeholder="Votre message…" required autocomplete="off"
                 style="flex:1;border:0;background:transparent;font:400 14px/1 var(--f-ui);color:var(--ink);outline:none"/>
          <button type="submit"
                  style="width:36px;height:36px;border-radius:999px;border:0;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:filter .15s"
                  @mouseenter="$el.style.filter='brightness(1.1)'" @mouseleave="$el.style.filter=''">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
              <path d="M4 10H16M12 6l4 4-4 4"/>
            </svg>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Auto-scroll to bottom of thread
  document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('msg-thread');
    if (el) el.scrollTop = el.scrollHeight;
  });
</script>
</x-layouts.app>
