<x-layouts.app title="Fil d'actualité" subtitle="Lycée Majakker">
<style>
html,body,#app-shell{height:100%;overflow:hidden}
.feed-grid{display:grid;grid-template-columns:1fr 320px;height:100%;overflow:hidden}
.feed-main{overflow:auto;border-right:0.5px solid var(--line)}
.feed-rail{overflow:auto;padding:18px 20px;display:flex;flex-direction:column;gap:18px}
.filter-btn{appearance:none;border:0;cursor:pointer;height:26px;padding:0 10px;border-radius:7px;font:500 12px/1 var(--f-ui);display:inline-flex;align-items:center;gap:6px;transition:background .14s}
</style>

<div class="feed-grid">

  {{-- CENTER: main feed --}}
  <main class="feed-main scroll">

    {{-- Filter bar --}}
    <div style="display:flex;align-items:center;gap:4px;padding:14px 18px;border-bottom:0.5px solid var(--line);background:var(--bg);position:sticky;top:0;z-index:5">
      @foreach([['all','Tout',$posts->total()],['ann','Annonces',null],['class','Classes',null],['clubs','Clubs',null]] as [$id,$lbl,$count])
      <a href="{{ route('feed', ['filter'=>$id]) }}"
         class="filter-btn"
         style="background:{{ $filter===$id ? 'var(--surface-2)' : 'transparent' }};color:{{ $filter===$id ? 'var(--ink)' : 'var(--ink-3)' }};text-decoration:none">
        {{ $lbl }}
        @if($count !== null)<span style="font:500 10px/1 var(--f-mono);color:var(--ink-4)">{{ $count }}</span>@endif
      </a>
      @endforeach
      <span style="flex:1"></span>
      <span class="eyebrow" style="font-size:9.5px">Trié par récence</span>
      <x-ui.icon name="chevronDown" size="11" style="color:var(--ink-3)"/>
    </div>

    {{-- Composer --}}
    <x-feed.composer/>

    {{-- Posts --}}
    @forelse($posts as $post)
      <x-feed.post-card :post="$post"/>
    @empty
      <div style="padding:60px 18px;text-align:center;color:var(--ink-3)">
        <x-ui.zellige-star size="40" color="var(--ink-4)" opacity="0.4"/>
        <div style="margin-top:12px;font:400 14px/1.5 var(--f-ui)">Aucune publication pour le moment.</div>
      </div>
    @endforelse

    {{-- Infinite scroll sentinel + injected posts --}}
    <div id="feed-extra"></div>
    <div id="scroll-sentinel" style="height:1px"></div>

    @if(!$posts->hasMorePages())
    <div style="padding:30px 18px;text-align:center;color:var(--ink-3);font:400 11.5px/1.5 var(--f-ui)">
      <x-ui.zellige-star size="28" color="var(--ink-4)" opacity="0.5"/>
      <div style="margin-top:6px">Vous avez tout vu — revenez plus tard ✦</div>
    </div>
    @endif
  </main>

  <script>
  (function () {
    let cursor = {{ $posts->last()?->id ?? 'null' }};
    let loading = false;
    let done    = {{ $posts->hasMorePages() ? 'false' : 'true' }};

    if (done) return;

    const sentinel = document.getElementById('scroll-sentinel');
    const extra    = document.getElementById('feed-extra');

    const observer = new IntersectionObserver(async (entries) => {
      if (!entries[0].isIntersecting || loading || done) return;
      loading = true;

      try {
        const res  = await fetch(`/api/feed?cursor=${cursor}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (!data.posts || data.posts.length === 0) { done = true; return; }

        data.posts.forEach(post => {
          const div = document.createElement('div');
          div.innerHTML = renderPost(post);
          extra.appendChild(div.firstChild);
        });

        cursor = data.next_cursor ?? null;
        if (!cursor) done = true;
      } catch(e) { console.error(e); }
      finally { loading = false; }
    }, { rootMargin: '200px' });

    observer.observe(sentinel);

    function renderPost(p) {
      const time = new Date(p.created_at).toLocaleDateString('fr-FR', { day:'numeric', month:'short' });
      return `<article style="padding:16px 18px;border-bottom:0.5px solid var(--line);display:grid;grid-template-columns:32px 1fr;gap:12px">
        <div class="avatar" style="width:28px;height:28px">${initials(p.user?.name??'?')}</div>
        <div>
          <div style="font:600 12.5px/1.2 var(--f-ui)">${e(p.user?.name??'')}
            <span style="font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-left:6px">· ${time}</span>
          </div>
          ${p.title ? `<div style="font:600 14px/1.35 var(--f-ui);margin-top:4px">${e(p.title)}</div>` : ''}
          <div style="font:400 13px/1.55 var(--f-ui);color:var(--ink-2);margin-top:4px;white-space:pre-wrap">${e(p.body)}</div>
        </div>
      </article>`;
    }

    function initials(name) {
      return name.split(' ').map(p => p[0]?.toUpperCase()||'').slice(0,2).join('');
    }
    function e(s) {
      return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
  })();
  </script>

  {{-- RIGHT: rail --}}
  <aside class="feed-rail scroll">

    {{-- Upcoming events --}}
    @if($events->count())
    <section style="display:flex;flex-direction:column;gap:10px">
      <header style="display:flex;align-items:baseline;justify-content:space-between">
        <span class="eyebrow">À venir cette semaine</span>
        <span class="eyebrow" style="color:var(--ink-4)">S{{ now()->weekOfYear }}</span>
      </header>
      @foreach($events as $event)
      <div style="display:grid;grid-template-columns:44px 1fr;gap:10px;padding:8px 10px;border-radius:10px;background:var(--surface);border:0.5px solid var(--line)">
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:4px 0;border-radius:7px;background:var(--c-{{ $event->color }}-soft)">
          <span style="font:500 8.5px/1 var(--f-mono);letter-spacing:.1em;color:var(--c-{{ $event->color }});opacity:.85">{{ strtoupper(substr($event->starts_at->locale('fr')->isoFormat('ddd'),0,3)) }}</span>
          <span style="font:600 14px/1 var(--f-ui);color:var(--c-{{ $event->color }});margin-top:2px">{{ $event->starts_at->format('d') }}</span>
        </div>
        <div style="min-width:0">
          <div style="font:500 11.5px/1.3 var(--f-ui)">{{ $event->title }}</div>
          <div style="font:400 10.5px/1.3 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $event->starts_at->format('H:i')  }}{{ $event->location ? ' · '.$event->location : '' }}</div>
        </div>
      </div>
      @endforeach
    </section>
    @endif

    {{-- Trending hashtags --}}
    @if($hashtags->count())
    <section style="display:flex;flex-direction:column;gap:8px">
      <span class="eyebrow">Sujets populaires</span>
      <div style="display:flex;flex-direction:column">
        @foreach($hashtags as $i => $tag)
        <div style="display:flex;align-items:center;gap:10px;padding:8px 4px;border-top:{{ $i===0 ? '0' : '0.5px solid var(--line)' }}">
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:6px;font:500 12px/1.2 var(--f-ui)">
              #{{ $tag->name }}
              @if($loop->first)<span class="chip chip-terracotta" style="height:16px;padding:0 6px;font-size:9px">EN DIRECT</span>@endif
            </div>
            <div style="font:400 10.5px/1.3 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ number_format($tag->posts_count) }} publications</div>
          </div>
          <x-ui.icon name="chevron" size="12" style="color:var(--ink-4)"/>
        </div>
        @endforeach
      </div>
    </section>
    @endif

    {{-- My classes/clubs --}}
    @if($groups->count())
    <section style="display:flex;flex-direction:column;gap:8px">
      <span class="eyebrow">{{ auth()->user()->isTeacher() ? 'Mes cours' : 'Mes classes' }}</span>
      @foreach($groups as $group)
      <a href="{{ route('groups.show', $group) }}" style="display:flex;align-items:center;gap:10px;padding:8px 0;text-decoration:none;color:inherit">
        <div style="width:8px;height:28px;border-radius:4px;background:var(--c-{{ $group->color }});opacity:.8;flex-shrink:0"></div>
        <div style="flex:1;min-width:0">
          <div style="font:500 11.5px/1.2 var(--f-ui)">{{ $group->name }}</div>
          <div style="font:400 10.5px/1.2 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $group->teacher?->name ?? '' }}</div>
        </div>
      </a>
      @endforeach
    </section>
    @endif

    {{-- Footer --}}
    <section style="margin-top:auto;padding-top:14px;border-top:0.5px solid var(--line);display:flex;flex-direction:column;gap:6px">
      <div style="display:flex;flex-wrap:wrap;gap:4px;font:400 10px/1.4 var(--f-ui);color:var(--ink-3)">
        <span>Aide</span><span>·</span><span>Charte</span><span>·</span><span>Confidentialité</span><span>·</span><span>Contact</span>
      </div>
      <div style="font:400 9.5px/1.4 var(--f-mono);color:var(--ink-4);letter-spacing:.06em">EDUSPHERE © {{ date('Y') }} — RABAT, MA</div>
    </section>
  </aside>
</div>
</x-layouts.app>
