<x-layouts.app title="Fil d'actualité" :subtitle="auth()->user()->school?->name ?? 'Majakker'">
<style>
/* ── Feed layout ── */
.feed-root{display:grid;grid-template-columns:1fr 300px;height:100%;overflow:hidden}
.feed-main{display:flex;flex-direction:column;overflow:hidden;border-right:0.5px solid var(--line)}
.feed-scroll{flex:1;overflow-y:auto;overflow-x:hidden}
.feed-rail{overflow-y:auto;overflow-x:hidden;padding:20px 18px 40px}

/* ── Filter bar ── */
.filter-bar{display:flex;align-items:center;gap:5px;padding:10px 18px;
  background:rgba(250,247,242,.88);backdrop-filter:blur(20px) saturate(180%);
  border-bottom:0.5px solid var(--line);position:sticky;top:0;z-index:10;flex-shrink:0}
.filter-pill{display:inline-flex;align-items:center;gap:5px;height:28px;padding:0 12px;
  border-radius:999px;font:500 12px/1 var(--f-ui);text-decoration:none;
  transition:all .18s cubic-bezier(.2,.7,.3,1);border:0.5px solid transparent;white-space:nowrap}
.filter-pill:not(.fp-active){color:var(--ink-3);background:transparent}
.filter-pill:not(.fp-active):hover{background:var(--surface-2);color:var(--ink);border-color:var(--line)}
.filter-pill.fp-active{background:var(--ink);color:var(--bg);box-shadow:0 3px 10px -2px rgba(26,22,20,.28)}
.fp-badge{min-width:16px;height:16px;padding:0 4px;border-radius:999px;
  font:600 9px/16px var(--f-mono);display:inline-block;text-align:center}
.fp-active .fp-badge{background:rgba(255,255,255,.2);color:inherit}
.filter-pill:not(.fp-active) .fp-badge{background:var(--surface-3);color:var(--ink-4)}

/* ── Post card ── */
.post-card{padding:18px 20px;border-bottom:0.5px solid var(--line);
  transition:background .18s;position:relative}
.post-card:hover{background:rgba(244,239,230,.4)}
.post-card.is-pinned{background:linear-gradient(175deg,rgba(212,160,74,.10) 0%,transparent 60%)}
.post-card.is-announcement{border-left:2.5px solid var(--ann-color)}
.pc-grid{display:grid;grid-template-columns:36px 1fr;gap:13px;align-items:start}

/* ── Action bar ── */
.act-btn{appearance:none;border:0;background:0;display:inline-flex;align-items:center;
  gap:5px;height:30px;padding:0 9px;border-radius:8px;font:500 12px/1 var(--f-ui);
  cursor:pointer;transition:all .14s;color:var(--ink-3)}
.act-btn:hover{background:var(--surface-2);color:var(--ink-2)}
.act-btn.is-liked{color:var(--c-terracotta)}
.act-btn.is-sparked{color:var(--c-saffron)}
.act-btn.is-active-cmt{color:var(--c-blue);background:var(--c-blue-soft)}
@keyframes pop{0%,100%{transform:scale(1)}50%{transform:scale(1.4)}}
.btn-pop{animation:pop .22s cubic-bezier(.2,.7,.3,1)}

/* ── Comments ── */
.cmt-section{border-top:0.5px solid var(--line);padding-top:12px;margin-top:4px;
  display:flex;flex-direction:column;gap:9px}
@keyframes cmt-in{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
.cmt-section{animation:cmt-in .18s ease-out}

/* ── Image grid ── */
.img-wrap{border-radius:12px;overflow:hidden;margin-top:2px}
.img-single img{width:100%;max-height:360px;object-fit:cover;display:block;
  transition:transform .4s cubic-bezier(.2,.7,.3,1)}
.img-pair{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.img-triple{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.img-triple .img-main{grid-row:span 2}
.img-quad{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.img-wrap img{width:100%;height:190px;object-fit:cover;display:block;
  transition:transform .4s cubic-bezier(.2,.7,.3,1);cursor:zoom-in}
.img-single img{height:auto;max-height:360px}
.img-wrap img:hover{transform:scale(1.025)}

/* ── Rail ── */
.rail-hd{font:500 10px/1 var(--f-mono);letter-spacing:.16em;text-transform:uppercase;
  color:var(--ink-3);display:flex;align-items:center;gap:8px;margin-bottom:8px}
.rail-hd::after{content:'';flex:1;height:0.5px;background:var(--line)}
.ev-card{display:grid;grid-template-columns:40px 1fr;gap:10px;padding:10px 12px;
  border-radius:12px;background:var(--surface);border:0.5px solid var(--line);
  transition:border-color .18s,box-shadow .18s;text-decoration:none;color:inherit}
.ev-card:hover{border-color:rgba(61,90,254,.25);box-shadow:0 4px 16px -8px rgba(26,22,20,.14)}
.ev-date{display:flex;flex-direction:column;align-items:center;justify-content:center;
  border-radius:9px;padding:5px 0}
.tag-item{display:flex;align-items:center;gap:8px;padding:9px 12px;
  text-decoration:none;color:inherit;border-top:0.5px solid var(--line);transition:background .14s}
.tag-item:hover{background:var(--surface-2)}
.tag-item:first-child{border-top:0}
.cls-item{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;
  text-decoration:none;color:inherit;transition:background .14s}
.cls-item:hover{background:var(--surface-2)}

/* ── Scrollbars ── */
.feed-scroll::-webkit-scrollbar,.feed-rail::-webkit-scrollbar{width:3px}
.feed-scroll::-webkit-scrollbar-thumb,.feed-rail::-webkit-scrollbar-thumb{background:var(--line-2);border-radius:2px}

/* ── Enter animations for first page ── */
@keyframes fd-up{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.post-card{animation:fd-up .38s cubic-bezier(.2,.7,.3,1) both}
.post-card:nth-child(1){animation-delay:.04s}
.post-card:nth-child(2){animation-delay:.08s}
.post-card:nth-child(3){animation-delay:.12s}
.post-card:nth-child(4){animation-delay:.16s}
.post-card:nth-child(5){animation-delay:.20s}

/* ── Responsive ── */
@media(max-width:860px){.feed-root{grid-template-columns:1fr}.feed-rail{display:none}}
</style>

@php
  $me = auth()->user();
  $hour = now()->hour;
  $greet = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon après-midi' : 'Bonsoir');
@endphp

<div class="feed-root">

  {{-- ═══════════════ FEED MAIN ═══════════════ --}}
  <main class="feed-main">

    {{-- Filter bar --}}
    <nav class="filter-bar">
      @foreach([
        ['all',   'Tout',      $posts->total()],
        ['ann',   'Annonces',  null],
        ['class', 'Classes',   null],
        ['clubs', 'Clubs',     null],
      ] as [$id,$lbl,$cnt])
      <a href="{{ route('feed',['filter'=>$id]) }}"
         class="filter-pill {{ $filter===$id?'fp-active':'' }}">
        @if($id==='all')
        <span style="width:6px;height:6px;border-radius:999px;flex-shrink:0;
              background:{{ $filter==='all'?'rgba(255,255,255,.6)':'var(--c-atlas)' }};
              box-shadow:{{ $filter==='all'?'none':'0 0 0 2px rgba(74,155,142,.2)' }}"></span>
        @endif
        {{ $lbl }}
        @if($cnt !== null)
        <span class="fp-badge">{{ number_format($cnt) }}</span>
        @endif
      </a>
      @endforeach
      <span style="flex:1"></span>
      <span style="font:500 10px/1 var(--f-mono);color:var(--ink-4);letter-spacing:.1em">RÉCENT</span>
    </nav>

    {{-- Scroll area --}}
    <div class="feed-scroll" id="feed-scroll">

      {{-- Composer --}}
      <div style="border-bottom:0.5px solid var(--line)">
        <x-feed.composer/>
      </div>

      {{-- Posts --}}
      @forelse($posts as $post)
        <x-feed.post-card :post="$post"/>
      @empty
      <div style="padding:80px 24px;text-align:center">
        <div style="width:64px;height:64px;border-radius:20px;background:var(--surface-2);
                    display:flex;align-items:center;justify-content:center;margin:0 auto 18px;
                    box-shadow:var(--sh-sm)">
          <x-ui.zellige-star size="32" color="var(--ink-4)" opacity="0.6"/>
        </div>
        <div style="font:400 20px/1.2 var(--f-display);color:var(--ink);margin-bottom:8px">
          Aucune publication
        </div>
        <div style="font:400 13.5px/1.6 var(--f-ui);color:var(--ink-3);max-width:320px;margin:0 auto">
          Soyez le premier à partager quelque chose avec votre école.
        </div>
      </div>
      @endforelse

      {{-- Infinite scroll sentinel --}}
      <div id="feed-extra"></div>
      <div id="scroll-sentinel" style="height:1px"></div>

      @if(!$posts->hasMorePages())
      <div style="padding:32px;text-align:center">
        <div style="display:inline-flex;align-items:center;gap:10px;color:var(--ink-4);
                    font:400 10.5px/1 var(--f-mono);letter-spacing:.1em">
          <x-ui.zellige-star size="16" color="var(--ink-4)" opacity="0.5"/>
          TOUT VU — REVENEZ PLUS TARD
          <x-ui.zellige-star size="16" color="var(--ink-4)" opacity="0.5"/>
        </div>
      </div>
      @endif
    </div>
  </main>

  {{-- ═══════════════ RIGHT RAIL ═══════════════ --}}
  <aside class="feed-rail">

    {{-- Greeting card --}}
    <div style="padding:16px 18px;border-radius:14px;margin-bottom:22px;position:relative;overflow:hidden;
                background:linear-gradient(135deg,var(--c-blue-soft) 0%,var(--c-atlas-soft) 100%);
                border:0.5px solid rgba(61,90,254,.14)">
      <div style="position:absolute;right:-10px;bottom:-10px;opacity:.1;pointer-events:none">
        <x-ui.zellige-star size="90" color="var(--c-blue)"/>
      </div>
      <div style="position:relative">
        <div style="font:500 10px/1 var(--f-mono);letter-spacing:.18em;text-transform:uppercase;color:var(--c-blue);margin-bottom:7px">{{ $greet }}</div>
        <div style="font:400 19px/1.15 var(--f-display);color:var(--ink);letter-spacing:-.02em">{{ $me->short_name }}</div>
        <div style="font:400 11.5px/1.3 var(--f-ui);color:var(--ink-3);margin-top:5px">
          {{ $me->role_label }}{{ $me->school ? ' · '.$me->school->name : '' }}
        </div>
        @if($me->school)
        <div style="display:flex;align-items:center;gap:5px;margin-top:10px;padding-top:10px;border-top:0.5px solid rgba(61,90,254,.14)">
          <span style="width:5px;height:5px;border-radius:999px;background:var(--c-atlas);box-shadow:0 0 0 2.5px rgba(74,155,142,.2)"></span>
          <span style="font:500 10.5px/1 var(--f-ui);color:var(--ink-3)">{{ number_format($me->school->student_count) }} élèves · {{ $me->school->city }}</span>
        </div>
        @endif
      </div>
    </div>

    {{-- Upcoming events --}}
    @if($events->count())
    <div style="margin-bottom:22px">
      <div class="rail-hd">
        <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="5" width="14" height="12" rx="1.5"/><path d="M3 9h14M7 3v3M13 3v3"/></svg>
        Prochains événements
      </div>
      <div style="display:flex;flex-direction:column;gap:7px">
        @foreach($events as $event)
        <a href="{{ route('events.index') }}" class="ev-card">
          <div class="ev-date" style="background:var(--c-{{ $event->color }}-soft)">
            <span style="font:500 8px/1 var(--f-mono);letter-spacing:.08em;color:var(--c-{{ $event->color }});text-transform:uppercase">
              {{ strtoupper(substr($event->starts_at->locale('fr')->isoFormat('MMM'),0,3)) }}
            </span>
            <span style="font:700 19px/1.1 var(--f-display);color:var(--c-{{ $event->color }});margin-top:2px">
              {{ $event->starts_at->format('d') }}
            </span>
          </div>
          <div style="min-width:0;display:flex;flex-direction:column;justify-content:center;gap:3px">
            <div style="font:600 12.5px/1.3 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              {{ $event->title }}
            </div>
            <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3)">
              {{ $event->starts_at->format('H\hi') }}{{ $event->location ? ' · '.$event->location : '' }}
            </div>
            <div style="margin-top:2px">
              <span style="font:500 9.5px/1 var(--f-mono);color:var(--c-{{ $event->color }});padding:1px 6px;border-radius:4px;background:var(--c-{{ $event->color }}-soft)">
                {{ $event->starts_at->diffForHumans() }}
              </span>
            </div>
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Trending hashtags --}}
    @if($hashtags->count())
    <div style="margin-bottom:22px">
      <div class="rail-hd">
        <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 9h12M5 14h10M7 4l-2 12M13 4l-2 12"/></svg>
        Tendances
      </div>
      <div style="background:var(--surface);border-radius:12px;border:0.5px solid var(--line);overflow:hidden">
        @foreach($hashtags as $i => $tag)
        @php $maxC = $hashtags->max('posts_count') ?: 1; $pct = round(($tag->posts_count/$maxC)*100); @endphp
        <a href="{{ route('search',['q'=>'#'.$tag->name]) }}" class="tag-item">
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px">
              <span style="font:600 12.5px/1 var(--f-ui)">#{{ $tag->name }}</span>
              @if($loop->first)
              <span style="padding:1px 5px;border-radius:4px;font:700 8px/1.4 var(--f-mono);background:var(--c-terracotta);color:#fff;letter-spacing:.05em">EN DIRECT</span>
              @endif
            </div>
            <div style="height:2.5px;background:var(--surface-3);border-radius:999px;overflow:hidden">
              <div style="height:100%;width:{{ $pct }}%;border-radius:999px;
                          background:{{ $i===0?'linear-gradient(90deg,var(--c-blue),var(--c-atlas))':'var(--ink-4)' }};
                          transition:width 1s cubic-bezier(.2,.7,.3,1)"></div>
            </div>
          </div>
          <span style="font:500 10px/1 var(--f-mono);color:var(--ink-4);flex-shrink:0">{{ number_format($tag->posts_count) }}</span>
        </a>
        @endforeach
      </div>
    </div>
    @endif

    {{-- My groups --}}
    @if($groups->count())
    <div style="margin-bottom:22px">
      <div class="rail-hd">
        <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 6h12M4 10h12M4 14h7"/></svg>
        {{ $me->isTeacher() ? 'Mes cours' : 'Mes classes' }}
      </div>
      <div style="display:flex;flex-direction:column;gap:2px">
        @foreach($groups as $group)
        <a href="{{ route('groups.show', $group) }}" class="cls-item">
          <div style="width:32px;height:32px;border-radius:9px;background:var(--c-{{ $group->color }}-soft);
                      display:flex;align-items:center;justify-content:center;flex-shrink:0;
                      border:0.5px solid rgba(0,0,0,.04)">
            <span style="width:9px;height:9px;border-radius:3px;background:var(--c-{{ $group->color }})"></span>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              {{ $group->name }}
            </div>
            <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">
              {{ $group->member_count }} membres
            </div>
          </div>
          <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--ink-4)" stroke-width="1.4" stroke-linecap="round"><path d="M8 5l6 5-6 5"/></svg>
        </a>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Footer --}}
    <div style="padding-top:14px;border-top:0.5px solid var(--line)">
      <div style="display:flex;flex-wrap:wrap;gap:5px 10px;font:400 10.5px/1.5 var(--f-ui);color:var(--ink-4)">
        @foreach(['Aide','Charte','Confidentialité','Contact'] as $lnk)
        <a href="#" style="color:inherit;text-decoration:none"
           onmouseenter="this.style.color='var(--ink-2)'" onmouseleave="this.style.color='var(--ink-4)'">{{ $lnk }}</a>
        @endforeach
      </div>
      <div style="font:400 9.5px/1 var(--f-mono);color:var(--ink-4);letter-spacing:.08em;margin-top:6px">
        EDUSPHERE © {{ date('Y') }} — RABAT, MA
      </div>
    </div>
  </aside>
</div>

{{-- ── Infinite scroll ── --}}
<script>
(function () {
  let cursor = {{ $posts->last()?->id ?? 'null' }};
  let loading = false;
  let done = {{ $posts->hasMorePages() ? 'false' : 'true' }};
  if (done) return;

  const sentinel = document.getElementById('scroll-sentinel');
  const extra    = document.getElementById('feed-extra');
  const scrollEl = document.getElementById('feed-scroll');

  const io = new IntersectionObserver(async (entries) => {
    if (!entries[0].isIntersecting || loading || done) return;
    loading = true;
    try {
      const res  = await fetch(`/api/feed?cursor=${cursor}`, { headers:{'X-Requested-With':'XMLHttpRequest'} });
      const data = await res.json();
      if (!data.posts?.length) { done = true; return; }
      data.posts.forEach(p => {
        const el = document.createElement('div');
        el.innerHTML = card(p);
        extra.appendChild(el.firstChild);
      });
      cursor = data.next_cursor ?? null;
      if (!cursor) done = true;
    } catch(e) {}
    finally { loading = false; }
  }, { root: scrollEl, rootMargin: '320px' });

  io.observe(sentinel);

  function card(p) {
    const name = p.user?.name ?? '?';
    const init = name.split(' ').map(w=>w[0]?.toUpperCase()||'').slice(0,2).join('');
    const time = new Date(p.created_at).toLocaleDateString('fr-FR',{day:'numeric',month:'short'});
    return `<article class="post-card pc-grid">
      <div class="avatar" style="width:36px;height:36px;font-size:13px;flex-shrink:0;border-radius:10px">${init}</div>
      <div style="min-width:0;display:flex;flex-direction:column;gap:6px">
        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap">
          <span style="font:600 13px/1.2 var(--f-ui)">${esc(name)}</span>
          <span style="font:400 10.5px/1 var(--f-mono);color:var(--ink-4)">${time}</span>
        </div>
        ${p.title?`<div style="font:700 14.5px/1.35 var(--f-ui)">${esc(p.title)}</div>`:''}
        <div style="font:400 13.5px/1.6 var(--f-ui);color:var(--ink-2);white-space:pre-wrap">${esc(p.body||'')}</div>
      </div>
    </article>`;
  }
  function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
})();
</script>
</x-layouts.app>
