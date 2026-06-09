<x-layouts.app title="Clubs">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">

  @if(session('success'))
  <div style="margin-bottom:20px;padding:12px 16px;border-radius:10px;background:rgba(16,185,129,.08);border:0.5px solid rgba(16,185,129,.25);color:#047857;font:500 13px/1.3 var(--f-ui)">
    {{ session('success') }}
  </div>
  @endif

  <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;gap:16px">
    <div>
      <span style="font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;color:var(--ink-3);display:inline-flex;align-items:center;gap:12px">
        <span style="display:inline-block;width:28px;height:0.5px;background:var(--ink-3)"></span>
        CLUBS
      </span>
      <h2 style="font:400 28px/1.1 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        Clubs de l'école
        <span style="font-size:16px;color:var(--ink-3)">({{ $allClubs->count() }})</span>
      </h2>
    </div>
    @if($myClubIds->count() > 0)
    <span style="display:inline-flex;align-items:center;gap:6px;height:32px;padding:0 14px;border-radius:9px;background:var(--c-saffron-soft);color:var(--c-saffron);font:600 11px/1 var(--f-mono)">
      <x-ui.icon name="clubs" size="13"/>
      {{ $myClubIds->count() }} REJOINT{{ $myClubIds->count() > 1 ? 'S' : '' }}
    </span>
    @endif
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:14px">
    @forelse($allClubs as $group)
    @php $isMember = $myClubIds->contains($group->id); @endphp
    <div style="display:flex;flex-direction:column;gap:14px;padding:20px;border-radius:14px;background:var(--surface);border:0.5px solid {{ $isMember ? 'var(--c-'.$group->color.')' : 'var(--line)' }};box-shadow:var(--sh-sm);transition:border-color .2s,box-shadow .2s"
         onmouseenter="this.style.borderColor='var(--c-{{ $group->color }})';this.style.boxShadow='var(--sh-md)'"
         onmouseleave="this.style.borderColor='{{ $isMember ? 'var(--c-'.$group->color.')' : 'var(--line)' }}';this.style.boxShadow='var(--sh-sm)'">

      {{-- Header --}}
      <div style="display:flex;align-items:flex-start;gap:12px">
        <div style="width:42px;height:42px;border-radius:12px;background:var(--c-{{ $group->color }}-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="clubs" size="19" style="color:var(--c-{{ $group->color }})"/>
        </div>
        <div style="flex:1;min-width:0">
          <a href="{{ route('groups.show', $group) }}"
             style="font:600 14px/1.3 var(--f-ui);color:var(--ink);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;transition:color .15s"
             onmouseenter="this.style.color='var(--c-{{ $group->color }})'" onmouseleave="this.style.color='var(--ink)'">
            {{ $group->name }}
          </a>
          <div style="font:500 10.5px/1 var(--f-mono);color:var(--c-{{ $group->color }});margin-top:4px">
            {{ $group->members_count }} membre{{ $group->members_count !== 1 ? 's' : '' }}
          </div>
        </div>
        @if($isMember)
        <span style="display:inline-flex;align-items:center;height:18px;padding:0 8px;border-radius:999px;background:var(--c-{{ $group->color }}-soft);color:var(--c-{{ $group->color }});font:600 9px/1 var(--f-mono);flex-shrink:0">
          MEMBRE
        </span>
        @endif
      </div>

      {{-- Description --}}
      @if($group->description)
      <p style="font:400 12.5px/1.5 var(--f-ui);color:var(--ink-3);margin:0">
        {{ \Illuminate\Support\Str::limit($group->description, 100) }}
      </p>
      @endif

      {{-- Teacher --}}
      @if($group->teacher)
      <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:9px;background:var(--surface-2)">
        <x-ui.avatar :name="$group->teacher->name" size="22"/>
        <div>
          <div style="font:500 11.5px/1.2 var(--f-ui);color:var(--ink-2)">{{ $group->teacher->short_name }}</div>
          <div style="font:400 10px/1 var(--f-mono);color:var(--ink-3);margin-top:2px">Superviseur</div>
        </div>
      </div>
      @endif

      {{-- Actions --}}
      <div style="display:flex;gap:8px">
        <a href="{{ route('groups.show', $group) }}"
           style="flex:1;display:inline-flex;align-items:center;justify-content:center;height:30px;border-radius:8px;border:0.5px solid var(--line-2);background:var(--surface-2);font:500 11.5px/1 var(--f-ui);color:var(--ink-2);text-decoration:none;transition:all .15s;gap:5px"
           onmouseenter="this.style.borderColor='rgba(126,91,239,.3)';this.style.color='#7E5BEF'" onmouseleave="this.style.borderColor='var(--line-2)';this.style.color='var(--ink-2)'">
          <x-ui.icon name="arrow" size="12"/>
          Voir
        </a>
        @if($isMember)
        <form action="{{ route('groups.leave', $group) }}" method="POST">@csrf
          <button type="submit"
                  style="height:30px;padding:0 14px;border-radius:8px;border:0.5px solid var(--line-2);background:var(--surface);font:500 11.5px/1 var(--f-ui);color:var(--ink-3);cursor:pointer;transition:all .15s"
                  onmouseenter="this.style.borderColor='rgba(220,38,38,.3)';this.style.color='#B91C1C'" onmouseleave="this.style.borderColor='var(--line-2)';this.style.color='var(--ink-3)'">
            Quitter
          </button>
        </form>
        @else
        <form action="{{ route('groups.join', $group) }}" method="POST" style="flex:1">@csrf
          <button type="submit"
                  style="width:100%;height:30px;border-radius:8px;border:0;background:linear-gradient(135deg,var(--c-{{ $group->color }}),var(--c-{{ $group->color }}));opacity:.9;color:#fff;font:600 11.5px/1 var(--f-ui);cursor:pointer;transition:opacity .15s"
                  onmouseenter="this.style.opacity='1'" onmouseleave="this.style.opacity='.9'">
            Rejoindre
          </button>
        </form>
        @endif
      </div>
    </div>
    @empty
    <div style="grid-column:1/-1;padding:80px 60px;text-align:center;color:var(--ink-3)">
      <div style="display:flex;justify-content:center;margin-bottom:16px">
        <x-ui.icon name="clubs" size="36" style="color:var(--ink-4)"/>
      </div>
      <div style="font:400 14px/1.5 var(--f-ui)">Aucun club dans cette école pour l'instant.</div>
    </div>
    @endforelse
  </div>
</div>
</x-layouts.app>
