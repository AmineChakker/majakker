<x-layouts.app :title="$group->name" :subtitle="$group->kind==='class'?'Classe':'Club'">
@include('components.feed.post-styles')
<div style="height:100%;display:flex;flex-direction:column">

  {{-- ── Group header ── --}}
  <div style="padding:18px 24px;background:var(--surface);border-bottom:0.5px solid var(--line);display:flex;align-items:center;gap:16px;flex-shrink:0">
    <div style="width:6px;height:40px;border-radius:3px;background:var(--c-{{ $group->color }});flex-shrink:0"></div>
    <div style="flex:1;min-width:0">
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <h2 style="font:600 18px/1.2 var(--f-ui);margin:0">{{ $group->name }}</h2>
        <span style="display:inline-flex;align-items:center;height:18px;padding:0 8px;border-radius:999px;background:var(--c-{{ $group->color }}-soft);color:var(--c-{{ $group->color }});font:600 9px/1 var(--f-mono)">
          {{ $group->kind === 'club' ? 'CLUB' : 'CLASSE' }}
        </span>
        @if($isMember)
        <span style="display:inline-flex;align-items:center;height:18px;padding:0 8px;border-radius:999px;background:rgba(16,185,129,.10);color:#047857;font:600 9px/1 var(--f-mono)">
          MEMBRE
        </span>
        @endif
      </div>
      <div style="font:400 12px/1 var(--f-ui);color:var(--ink-3);margin-top:5px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <span>{{ $group->member_count }} membre{{ $group->member_count !== 1 ? 's' : '' }}</span>
        @if($group->teacher)
        <span style="display:inline-block;width:3px;height:3px;border-radius:999px;background:var(--ink-4)"></span>
        <span>Superviseur : {{ $group->teacher->name }}</span>
        @endif
      </div>
      @if($group->description)
      <div style="font:400 12px/1.5 var(--f-ui);color:var(--ink-3);margin-top:6px;max-width:640px">
        {{ $group->description }}
      </div>
      @endif
    </div>

    {{-- Join / leave for clubs only --}}
    @if($group->kind === 'club')
      @if($isMember)
      <form action="{{ route('groups.leave', $group) }}" method="POST" style="flex-shrink:0">
        @csrf
        <button type="submit"
                style="height:34px;padding:0 16px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:500 12px/1 var(--f-ui);color:var(--ink-2);cursor:pointer;transition:all .15s"
                onmouseenter="this.style.borderColor='rgba(220,38,38,.3)';this.style.color='#B91C1C'" onmouseleave="this.style.borderColor='var(--line-2)';this.style.color='var(--ink-2)'">
          Quitter le club
        </button>
      </form>
      @else
      <form action="{{ route('groups.join', $group) }}" method="POST" style="flex-shrink:0">
        @csrf
        <button type="submit"
                style="height:34px;padding:0 18px;border-radius:9px;border:0;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;font:600 12px/1 var(--f-ui);cursor:pointer;box-shadow:0 4px 14px -4px rgba(94,57,224,.4);transition:filter .18s"
                onmouseenter="this.style.filter='brightness(1.1)'" onmouseleave="this.style.filter=''">
          Rejoindre
        </button>
      </form>
      @endif
    @endif
  </div>

  {{-- ── Feed area ── --}}
  <div style="flex:1;overflow:auto" class="scroll">

    @if($canPost)
      {{-- Composer for members and supervisors --}}
      <x-feed.composer :groupId="$group->id"/>
      <div style="height:0.5px;background:var(--line)"></div>
    @elseif($group->kind === 'club' && !$canViewPosts)
      {{-- Gate for non-members --}}
      <div style="padding:48px 32px;text-align:center;border-bottom:0.5px solid var(--line)">
        <div style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:16px;background:var(--c-{{ $group->color }}-soft);margin-bottom:16px">
          <x-ui.icon name="clubs" size="26" style="color:var(--c-{{ $group->color }})"/>
        </div>
        <h3 style="font:500 16px/1.3 var(--f-ui);color:var(--ink);margin:0 0 8px">Ce club est réservé à ses membres</h3>
        <p style="font:400 13px/1.5 var(--f-ui);color:var(--ink-3);margin:0 0 20px;max-width:360px;margin-left:auto;margin-right:auto">
          Rejoignez ce club pour accéder aux discussions et y contribuer.
          {{ $group->member_count }} membre{{ $group->member_count !== 1 ? 's' : '' }} actif{{ $group->member_count !== 1 ? 's' : '' }}.
        </p>
        <form action="{{ route('groups.join', $group) }}" method="POST" style="display:inline">
          @csrf
          <button type="submit"
                  style="height:38px;padding:0 22px;border-radius:10px;border:0;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;font:600 13px/1 var(--f-ui);cursor:pointer;box-shadow:0 4px 14px -4px rgba(94,57,224,.4)">
            Rejoindre le club
          </button>
        </form>
      </div>
    @endif

    @if($canViewPosts)
      @if(session('success'))
      <div style="margin:14px 18px;padding:12px 16px;border-radius:10px;background:rgba(16,185,129,.08);border:0.5px solid rgba(16,185,129,.25);color:#047857;font:500 13px/1.3 var(--f-ui)">
        {{ session('success') }}
      </div>
      @endif

      @forelse($posts as $post)
        <x-feed.post-card :post="$post"/>
      @empty
        <div style="padding:80px 60px;text-align:center;color:var(--ink-3)">
          <div style="margin-bottom:12px;opacity:.5">
            <x-ui.icon name="feed" size="28"/>
          </div>
          <div style="font:400 14px/1.5 var(--f-ui)">Aucune publication dans ce groupe pour l'instant.</div>
          @if($canPost)
          <div style="font:400 12px/1.4 var(--f-ui);margin-top:6px">Soyez le premier à partager quelque chose !</div>
          @endif
        </div>
      @endforelse
    @endif

  </div>
</div>
</x-layouts.app>
