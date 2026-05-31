@props(['post'])
@php
  $me      = auth()->user();
  $liked   = $me->hasReacted($post, 'like');
  $sparked = $me->hasReacted($post, 'spark');

  // Role visuals
  $roleColors  = ['teacher'=>'var(--c-atlas)','director'=>'var(--c-saffron)','student'=>'var(--c-blue)'];
  $roleChips   = ['teacher'=>'chip-atlas','director'=>'chip-saffron','student'=>'chip-blue'];
  $roleLabels  = ['teacher'=>'Professeur','director'=>'Directeur','student'=>'Étudiant'];
  $roleColor   = $roleColors[$post->user->role] ?? 'var(--c-blue)';
  $roleChip    = $roleChips[$post->user->role]  ?? 'chip-blue';
  $roleLabel   = $roleLabels[$post->user->role] ?? '';

  // Attachment groups
  $images = $post->attachments->where('kind','image')->values();
  $videos = $post->attachments->where('kind','video')->values();
  $files  = $post->attachments->where('kind','file')->values();
  $polls  = $post->attachments->where('kind','poll')->values();

  // Card styling
  $isAnn    = $post->is_announcement;
  $isPinned = $post->is_pinned;
  $cardCls  = 'post-card'.($isPinned?' is-pinned':'').($isAnn?' is-announcement':'');
  $annStyle = $isAnn ? "--ann-color:{$roleColor}" : '';
@endphp

<article class="{{ $cardCls }}" style="{{ $annStyle }}"
         x-data="postCard({{ $post->id }}, {{ (int)$liked }}, {{ $post->likes_count }}, {{ (int)$sparked }}, {{ $post->sparks_count }})">

  @if($isPinned)
  {{-- Pin strip --}}
  <div style="display:flex;align-items:center;gap:5px;margin-bottom:10px;
              font:600 9.5px/1 var(--f-mono);letter-spacing:.08em;text-transform:uppercase;color:#8A6520">
    <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L13 6L17 7L13 11L13 17L10 14L7 17L7 11L3 7L7 6Z"/></svg>
    Épinglé
  </div>
  @endif

  <div class="pc-grid">
    {{-- Avatar --}}
    <a href="{{ route('profile.show',$post->user) }}" style="text-decoration:none;flex-shrink:0">
      <x-ui.avatar :name="$post->user->name" size="36" :ring="false"/>
    </a>

    <div style="display:flex;flex-direction:column;gap:8px;min-width:0">

      {{-- Header --}}
      <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;min-width:0">
        <a href="{{ route('profile.show',$post->user) }}"
           style="font:700 13.5px/1.2 var(--f-ui);color:var(--ink);text-decoration:none"
           onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">
          {{ $post->user->name }}
        </a>
        @if($roleLabel)
        <span class="chip {{ $roleChip }}" style="font-size:9.5px;height:17px">{{ $roleLabel }}</span>
        @endif
        @if($isAnn)
        <span style="padding:1px 6px;border-radius:4px;font:700 8px/1.4 var(--f-mono);
                     background:{{ $roleColor }};color:#fff;letter-spacing:.05em">ANNONCE</span>
        @endif
        <span style="font:400 11px/1 var(--f-ui);color:var(--ink-4)">·</span>
        <span style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">{{ $post->where }}</span>
        <span style="font:400 11px/1 var(--f-ui);color:var(--ink-4)">·</span>
        <span style="font:400 11px/1 var(--f-mono);color:var(--ink-4)">{{ $post->time_ago }}</span>
        <span style="flex:1"></span>
        {{-- Context menu --}}
        @if($me->id===$post->user_id || $me->isDirector() || $me->isAdmin())
        <div x-data="{open:false}" style="position:relative;flex-shrink:0">
          <button @click="open=!open" @click.outside="open=false"
                  style="width:24px;height:24px;border-radius:6px;border:0;background:0;cursor:pointer;
                         display:flex;align-items:center;justify-content:center;color:var(--ink-4);transition:all .14s"
                  @mouseenter="$el.style.background='var(--surface-2)';$el.style.color='var(--ink-2)'"
                  @mouseleave="$el.style.background='';$el.style.color='var(--ink-4)'">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
              <circle cx="10" cy="5"  r="1.4"/><circle cx="10" cy="10" r="1.4"/><circle cx="10" cy="15" r="1.4"/>
            </svg>
          </button>
          <div x-show="open" x-cloak
               style="position:absolute;right:0;top:28px;background:var(--surface);border:0.5px solid var(--line-2);
                      border-radius:10px;box-shadow:var(--sh-lg);min-width:150px;overflow:hidden;z-index:20">
            @if($me->isDirector() || $me->isAdmin())
            <form action="{{ route('posts.pin',$post) }}" method="POST">
              @csrf
              <button type="submit"
                      style="width:100%;padding:9px 14px;border:0;background:0;text-align:left;
                             font:400 12.5px/1 var(--f-ui);color:var(--ink-2);cursor:pointer;
                             display:flex;align-items:center;gap:8px"
                      @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background=''">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M10 3L13 6L17 7L13 11L13 17L10 14L7 17L7 11L3 7L7 6Z"/></svg>
                {{ $post->is_pinned ? 'Désépingler' : 'Épingler' }}
              </button>
            </form>
            @endif
            <form action="{{ route('posts.destroy',$post) }}" method="POST"
                  onsubmit="return confirm('Supprimer cette publication ?')">
              @csrf @method('DELETE')
              <button type="submit"
                      style="width:100%;padding:9px 14px;border:0;background:0;text-align:left;
                             font:400 12.5px/1 var(--f-ui);color:#B91C1C;cursor:pointer;
                             display:flex;align-items:center;gap:8px"
                      @mouseenter="$el.style.background='rgba(220,38,38,.05)'" @mouseleave="$el.style.background=''">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M4 6h12M8 6V4h4v2M7 6v10a1 1 0 001 1h4a1 1 0 001-1V6"/></svg>
                Supprimer
              </button>
            </form>
          </div>
        </div>
        @endif
      </div>

      {{-- Title --}}
      @if($post->title)
      <div style="font:700 15px/1.35 var(--f-ui);letter-spacing:-.01em;color:var(--ink)">
        {{ $post->title }}
      </div>
      @endif

      {{-- Body --}}
      @if($post->body)
      <div style="font:400 14px/1.65 var(--f-ui);color:var(--ink-2);white-space:pre-wrap">{{ $post->body }}</div>
      @endif

      {{-- ── Images ── --}}
      @if($images->count())
      @php $n = min($images->count(), 4); @endphp
      <div class="img-wrap {{ $n===1?'img-single':($n===2?'img-pair':($n===3?'img-triple':'img-quad')) }}">
        @foreach($images->take(4) as $i => $img)
        <div style="position:relative;overflow:hidden;{{ $n===3&&$i===0?'grid-row:span 2':''; }}">
          <img src="{{ $img->url }}" alt="{{ $img->original_name }}"
               loading="lazy"
               style="width:100%;{{ $n===1?'height:auto;max-height:380px':'height:'.($n===3&&$i===0?'100%':'180px') }};object-fit:cover;display:block;transition:transform .4s cubic-bezier(.2,.7,.3,1);cursor:zoom-in"
               onmouseenter="this.style.transform='scale(1.03)'" onmouseleave="this.style.transform='scale(1)'"/>
          @if($i===3 && $images->count() > 4)
          <div style="position:absolute;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center">
            <span style="font:700 26px/1 var(--f-display);color:#fff">+{{ $images->count()-4 }}</span>
          </div>
          @endif
        </div>
        @endforeach
      </div>
      @endif

      {{-- ── Videos ── --}}
      @foreach($videos as $vid)
      <div style="border-radius:12px;overflow:hidden;background:#0a0a0a;border:0.5px solid var(--line)">
        <video src="{{ $vid->url }}" controls preload="metadata"
               style="width:100%;max-height:360px;display:block"></video>
        <div style="padding:6px 12px;font:400 10.5px/1 var(--f-ui);color:rgba(255,255,255,.4);background:#111">
          {{ $vid->original_name }}
        </div>
      </div>
      @endforeach

      {{-- ── Files ── --}}
      @if($files->count())
      <div style="display:flex;flex-direction:column;gap:5px">
        @foreach($files as $att)
        @php
          $ext = strtoupper(pathinfo($att->original_name ?? '', PATHINFO_EXTENSION));
          $colors = ['PDF'=>['#FEF2F2','#B91C1C'],'DOC'=>['#EFF6FF','#1D4ED8'],'DOCX'=>['#EFF6FF','#1D4ED8'],
                     'XLS'=>['#F0FDF4','#166534'],'XLSX'=>['#F0FDF4','#166534'],'PPT'=>['#FFF7ED','#C2410C'],
                     'PPTX'=>['#FFF7ED','#C2410C'],'ZIP'=>['#F5F3FF','#6D28D9']];
          [$bg,$fg] = $colors[$ext] ?? ['var(--c-blue-soft)','var(--c-blue)'];
        @endphp
        <a href="{{ $att->url }}" target="_blank" rel="noopener"
           style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:11px;
                  border:0.5px solid var(--line);background:var(--surface);text-decoration:none;
                  transition:border-color .16s,box-shadow .16s"
           onmouseenter="this.style.borderColor='rgba(61,90,254,.28)';this.style.boxShadow='0 4px 16px -8px rgba(26,22,20,.14)'"
           onmouseleave="this.style.borderColor='var(--line)';this.style.boxShadow='none'">
          <div style="width:38px;height:38px;border-radius:9px;background:{{ $bg }};color:{{ $fg }};
                      display:flex;align-items:center;justify-content:center;font:700 9.5px/1 var(--f-mono);flex-shrink:0">
            {{ $ext ?: 'FILE' }}
          </div>
          <div style="flex:1;min-width:0">
            <div style="font:500 12.5px/1.3 var(--f-ui);color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              {{ $att->original_name }}
            </div>
            <div style="font:400 10.5px/1 var(--f-mono);color:var(--ink-3);margin-top:3px">{{ $att->file_size_human }}</div>
          </div>
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="var(--ink-4)" stroke-width="1.4" stroke-linecap="round">
            <path d="M10 3v10M5 8l5 5 5-5"/><path d="M3 17h14"/>
          </svg>
        </a>
        @endforeach
      </div>
      @endif

      {{-- ── Polls ── --}}
      @foreach($polls as $att)
      @if($att->poll)<x-feed.poll :poll="$att->poll"/>@endif
      @endforeach

      {{-- ── Hashtags ── --}}
      @if($post->hashtags->count())
      <div style="display:flex;flex-wrap:wrap;gap:4px">
        @foreach($post->hashtags as $tag)
        <a href="{{ route('search',['q'=>'#'.$tag->name]) }}"
           style="font:500 11.5px/1 var(--f-ui);color:var(--c-blue);text-decoration:none;padding:2px 0"
           onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">
          #{{ $tag->name }}
        </a>
        @endforeach
      </div>
      @endif

      {{-- ── Action bar ── --}}
      <div style="display:flex;align-items:center;gap:1px;margin:-2px -8px 0">

        {{-- Like --}}
        <button @click="toggleLike(); $refs.likeIcon.classList.add('btn-pop'); setTimeout(()=>$refs.likeIcon.classList.remove('btn-pop'),250)"
                class="act-btn" :class="liked?'is-liked':''">
          <svg x-ref="likeIcon" width="16" height="16" viewBox="0 0 20 20" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"
               :fill="liked?'var(--c-terracotta)':'none'" :stroke="liked?'var(--c-terracotta)':'currentColor'">
            <path d="M10 16C4 12 2 9 2 6.5A3.5 3.5 0 0 1 10 5 3.5 3.5 0 0 1 18 6.5C18 9 16 12 10 16z"/>
          </svg>
          <span x-text="likeCount" style="min-width:14px;text-align:left"></span>
        </button>

        {{-- Comments --}}
        <button @click="toggleComments()"
                class="act-btn" :class="showComments?'is-active-cmt':''">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
            <path d="M3 5h14v9H10l-4 3V14H3z"/>
          </svg>
          <span data-comment-count="{{ $post->comments_count }}" x-text="commentCount" style="min-width:14px;text-align:left"></span>
        </button>

        {{-- Spark --}}
        <button @click="toggleSpark(); $refs.sparkIcon.classList.add('btn-pop'); setTimeout(()=>$refs.sparkIcon.classList.remove('btn-pop'),250)"
                class="act-btn" :class="sparked?'is-sparked':''">
          <svg x-ref="sparkIcon" width="16" height="16" viewBox="0 0 20 20" stroke-width="1.4" stroke-linecap="round"
               :fill="sparked?'var(--c-saffron)':'none'" :stroke="sparked?'var(--c-saffron)':'currentColor'">
            <path d="M10 3L11 8L16 9L11 10L10 15L9 10L4 9L9 8z"/>
          </svg>
          <span x-text="sparkCount" style="min-width:14px;text-align:left"></span>
        </button>

        {{-- Share --}}
        <button class="act-btn"
                @click="navigator.clipboard?.writeText('{{ url('/posts/'.$post->id) }}').then(()=>{$el.style.color='var(--c-atlas)'}).catch(()=>{})">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
            <path d="M14 4L17 7L14 10M17 7H7a4 4 0 00-4 4v3"/>
          </svg>
        </button>
      </div>

      {{-- ── Inline comments ── --}}
      <div x-show="showComments"
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="cmt-section">

          {{-- Comments list --}}
          <template x-for="c in comments" :key="c.id??c.body">
            <div style="display:grid;grid-template-columns:28px 1fr;gap:8px;align-items:start">
              <div style="width:28px;height:28px;border-radius:9px;background:var(--surface-3);
                          display:flex;align-items:center;justify-content:center;
                          font:600 10px/1 var(--f-ui);color:var(--ink-3);flex-shrink:0"
                   x-text="(c.user?.name||'?').split(' ').map(p=>p[0]).join('').toUpperCase().slice(0,2)"></div>
              <div style="background:var(--surface-2);border-radius:0 11px 11px 11px;padding:9px 12px">
                <div style="font:600 11.5px/1.2 var(--f-ui);color:var(--ink);margin-bottom:3px" x-text="c.user?.name||'Anonyme'"></div>
                <div style="font:400 13px/1.55 var(--f-ui);color:var(--ink-2)" x-text="c.body"></div>
                <div style="font:400 10px/1 var(--f-mono);color:var(--ink-4);margin-top:5px" x-text="timeAgo(c.created_at)"></div>
              </div>
            </div>
          </template>

          <template x-if="commentsLoaded && comments.length===0">
            <p style="font:400 12.5px/1 var(--f-ui);color:var(--ink-3);text-align:center;padding:4px 0;margin:0">
              Commencez la conversation ✦
            </p>
          </template>

          {{-- Comment input --}}
          <div style="display:grid;grid-template-columns:28px 1fr;gap:8px;align-items:center">
            <x-ui.avatar :name="$me->name" size="28"/>
            <div style="display:flex;align-items:center;gap:8px;background:var(--surface-2);
                        border-radius:20px;padding:5px 5px 5px 14px;border:0.5px solid var(--line);
                        transition:border-color .16s"
                 @focusin="$el.style.borderColor='var(--c-blue)'" @focusout="$el.style.borderColor='var(--line)'">
              <input type="text" x-model="commentText" placeholder="Votre commentaire…"
                     @keydown.enter.prevent="submitComment()"
                     style="flex:1;border:0;background:transparent;font:400 13.5px/1 var(--f-ui);
                            color:var(--ink);outline:none"/>
              <button @click="submitComment()"
                      :disabled="!commentText.trim()||submitting"
                      :style="commentText.trim()&&!submitting?'background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;cursor:pointer':'background:var(--surface-3);color:var(--ink-4);cursor:not-allowed'"
                      style="width:30px;height:30px;border-radius:999px;border:0;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .18s">
                <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
                  <path d="M4 10H16M12 6l4 4-4 4"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</article>
