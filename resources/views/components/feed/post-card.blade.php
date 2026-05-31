@props(['post'])
@php
    $user    = auth()->user();
    $liked   = $user->hasReacted($post, 'like');
    $sparked = $user->hasReacted($post, 'spark');

    $roleChip  = match($post->user->role) { 'teacher'=>'chip-atlas', 'director'=>'chip-saffron', default=>'chip-blue' };
    $roleLabel = match($post->user->role) { 'teacher'=>'Professeur', 'director'=>'Directeur', 'student'=>'Étudiant', default=>'' };
    $pinnedBg  = $post->is_pinned ? 'linear-gradient(180deg,var(--c-saffron-soft) 0%,transparent 60%)' : 'transparent';

    $images = $post->attachments->where('kind','image');
    $videos = $post->attachments->where('kind','video');
    $files  = $post->attachments->where('kind','file');
    $polls  = $post->attachments->where('kind','poll');
@endphp

<article style="padding:16px 18px;border-bottom:0.5px solid var(--line);display:grid;grid-template-columns:36px 1fr;gap:12px;background:{{ $pinnedBg }};transition:background .16s"
         x-data="postCard({{ $post->id }}, {{ (int)$liked }}, {{ $post->likes_count }}, {{ (int)$sparked }}, {{ $post->sparks_count }})"
         @mouseenter="{{ !$post->is_pinned ? 'true' : 'false' }} && ($el.style.background='rgba(244,239,230,0.45)')"
         @mouseleave="$el.style.background='{{ $pinnedBg }}'">

  {{-- Avatar --}}
  <div>
    <x-ui.avatar :name="$post->user->name" size="34"/>
  </div>

  <div style="display:flex;flex-direction:column;gap:8px;min-width:0">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap">
      <a href="{{ route('profile.show',$post->user) }}" style="font:600 13px/1.2 var(--f-ui);color:var(--ink);text-decoration:none"
         onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">{{ $post->user->name }}</a>
      @if($roleLabel)<span class="chip {{ $roleChip }}" style="font-size:9.5px">{{ $roleLabel }}</span>@endif
      <span style="color:var(--ink-4);font-size:11px">·</span>
      <span style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">{{ $post->where }}</span>
      <span style="color:var(--ink-4);font-size:11px">·</span>
      <span style="font:400 11px/1 var(--f-mono);color:var(--ink-4)">{{ $post->time_ago }}</span>
      @if($post->is_pinned)
      <span style="margin-left:auto;display:inline-flex;align-items:center;gap:4px;color:#8A6520;font:600 9.5px/1 var(--f-mono);letter-spacing:.06em">
        <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L13 6L17 7L13 11L13 17L10 14L7 17L7 11L3 7L7 6Z"/></svg>
        ÉPINGLÉ
      </span>
      @endif
      {{-- ⋯ menu --}}
      @if($user->id === $post->user_id || $user->isDirector() || $user->isAdmin())
      <div x-data="{open:false}" style="position:relative;margin-left:{{ $post->is_pinned ? '0' : 'auto' }}">
        <button @click="open=!open" @click.outside="open=false"
                style="width:24px;height:24px;border-radius:6px;border:0;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ink-4)"
                @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background=''">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><circle cx="10" cy="4" r="1.5"/><circle cx="10" cy="10" r="1.5"/><circle cx="10" cy="16" r="1.5"/></svg>
        </button>
        <div x-show="open" x-cloak style="position:absolute;right:0;top:28px;background:var(--surface);border:0.5px solid var(--line-2);border-radius:10px;box-shadow:var(--sh-lg);min-width:145px;overflow:hidden;z-index:20">
          @if($user->isDirector() || $user->isAdmin())
          <form action="{{ route('posts.pin',$post) }}" method="POST">
            @csrf
            <button type="submit" style="width:100%;padding:9px 14px;border:0;background:transparent;text-align:left;font:400 12.5px/1 var(--f-ui);color:var(--ink-2);cursor:pointer;display:flex;align-items:center;gap:8px"
                    @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background=''">
              <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M10 3L13 6L17 7L13 11L13 17L10 14L7 17L7 11L3 7L7 6Z"/></svg>
              {{ $post->is_pinned ? 'Désépingler' : 'Épingler' }}
            </button>
          </form>
          @endif
          <form action="{{ route('posts.destroy',$post) }}" method="POST"
                onsubmit="return confirm('Supprimer cette publication ?')">
            @csrf @method('DELETE')
            <button type="submit" style="width:100%;padding:9px 14px;border:0;background:transparent;text-align:left;font:400 12.5px/1 var(--f-ui);color:#B91C1C;cursor:pointer;display:flex;align-items:center;gap:8px"
                    @mouseenter="$el.style.background='rgba(220,38,38,.05)'" @mouseleave="$el.style.background=''">
              <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M4 6h12M8 6V4h4v2M7 6v10a1 1 0 001 1h4a1 1 0 001-1V6"/></svg>
              Supprimer
            </button>
          </form>
        </div>
      </div>
      @endif
    </div>

    {{-- Title --}}
    @if($post->title)
    <div style="font:700 14.5px/1.35 var(--f-ui);letter-spacing:-.01em;color:var(--ink)">{{ $post->title }}</div>
    @endif

    {{-- Body --}}
    @if($post->body)
    <div style="font:400 13.5px/1.6 var(--f-ui);color:var(--ink-2);white-space:pre-wrap">{{ $post->body }}</div>
    @endif

    {{-- ── Images grid ── --}}
    @if($images->count())
    @php $imgCount = $images->count(); @endphp
    <div style="border-radius:12px;overflow:hidden;display:grid;gap:2px;
                {{ $imgCount === 1 ? '' : ($imgCount === 2 ? 'grid-template-columns:1fr 1fr' : ($imgCount >= 3 ? 'grid-template-columns:1fr 1fr' : '')) }}">
      @foreach($images->take(4) as $i => $img)
      <div style="{{ $imgCount === 3 && $i === 0 ? 'grid-column:1/-1;' : '' }}overflow:hidden;position:relative">
        <img src="{{ $img->url }}" alt="{{ $img->original_name }}"
             loading="lazy"
             style="width:100%;height:{{ $imgCount === 1 ? '360px' : '190px' }};object-fit:cover;display:block;cursor:pointer;transition:transform .4s cubic-bezier(.2,.7,.3,1)"
             onmouseenter="this.style.transform='scale(1.025)'" onmouseleave="this.style.transform='scale(1)'"/>
        @if($i === 3 && $imgCount > 4)
        <div style="position:absolute;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;color:#fff;font:700 24px/1 var(--f-display)">
          +{{ $imgCount - 4 }}
        </div>
        @endif
      </div>
      @endforeach
    </div>
    @endif

    {{-- ── Videos ── --}}
    @foreach($videos as $vid)
    <div style="border-radius:12px;overflow:hidden;background:#000;border:0.5px solid var(--line)">
      <video src="{{ $vid->url }}" controls preload="metadata"
             style="width:100%;max-height:360px;display:block">
        Votre navigateur ne supporte pas la lecture vidéo.
      </video>
      <div style="padding:6px 10px;font:400 11px/1 var(--f-ui);color:rgba(255,255,255,.5);background:#111">
        {{ $vid->original_name }}
      </div>
    </div>
    @endforeach

    {{-- ── Files ── --}}
    @if($files->count())
    <div style="display:flex;flex-direction:column;gap:6px">
      @foreach($files as $att)
      @php
        $ext = strtoupper(pathinfo($att->original_name ?? '', PATHINFO_EXTENSION));
        $colors = ['PDF'=>['#FEF2F2','#B91C1C'],'DOC'=>['#EFF6FF','#1D4ED8'],'DOCX'=>['#EFF6FF','#1D4ED8'],'XLS'=>['#F0FDF4','#166534'],'XLSX'=>['#F0FDF4','#166534'],'PPT'=>['#FFF7ED','#C2410C'],'PPTX'=>['#FFF7ED','#C2410C'],'ZIP'=>['#F5F3FF','#6D28D9']];
        [$bg,$fg] = $colors[$ext] ?? ['var(--c-blue-soft)','var(--c-blue)'];
      @endphp
      <a href="{{ $att->url }}" target="_blank" rel="noopener"
         style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;border:0.5px solid var(--line);background:var(--surface);text-decoration:none;transition:border-color .15s,box-shadow .15s"
         onmouseenter="this.style.borderColor='rgba(61,90,254,.3)';this.style.boxShadow='var(--sh-sm)'" onmouseleave="this.style.borderColor='var(--line)';this.style.boxShadow='none'">
        <div style="width:38px;height:38px;border-radius:9px;background:{{ $bg }};color:{{ $fg }};display:flex;align-items:center;justify-content:center;font:700 9.5px/1 var(--f-mono);flex-shrink:0">
          {{ $ext ?: '?' }}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font:500 12.5px/1.3 var(--f-ui);color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $att->original_name }}</div>
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
    @if($att->poll)
    @include('components.feed.poll', ['poll' => $att->poll])
    @endif
    @endforeach

    {{-- ── Hashtags ── --}}
    @if($post->hashtags->count())
    <div style="display:flex;flex-wrap:wrap;gap:6px">
      @foreach($post->hashtags as $tag)
      <a href="{{ route('search',['q'=>'#'.$tag->name]) }}"
         style="font:500 11.5px/1 var(--f-ui);color:var(--c-blue);text-decoration:none"
         onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">
        #{{ $tag->name }}
      </a>
      @endforeach
    </div>
    @endif

    {{-- ── Action bar ── --}}
    <div style="display:flex;align-items:center;gap:2px;margin-left:-8px;padding-top:2px">

      {{-- Like --}}
      <button @click="toggleLike()"
              style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:32px;padding:0 10px;border-radius:8px;font:500 12px/1 var(--f-ui);cursor:pointer;transition:all .14s"
              :style="liked ? 'color:var(--c-terracotta)' : 'color:var(--ink-3)'"
              @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
        <svg width="16" height="16" viewBox="0 0 20 20" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"
             :fill="liked ? 'var(--c-terracotta)' : 'none'" :stroke="liked ? 'var(--c-terracotta)' : 'currentColor'">
          <path d="M10 16C4 12 2 9 2 6.5A3.5 3.5 0 0 1 10 5A3.5 3.5 0 0 1 18 6.5C18 9 16 12 10 16z"/>
        </svg>
        <span x-text="likeCount"></span>
      </button>

      {{-- Comments --}}
      <button @click="toggleComments()"
              style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:32px;padding:0 10px;border-radius:8px;font:500 12px/1 var(--f-ui);cursor:pointer;transition:all .14s"
              :style="showComments ? 'color:var(--c-blue);background:var(--c-blue-soft)' : 'color:var(--ink-3)'"
              @mouseenter="!showComments && ($el.style.background='var(--surface-2)')" @mouseleave="!showComments && ($el.style.background='transparent')">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
          <path d="M3 5h14v9H10l-4 3V14H3z"/>
        </svg>
        <span data-comment-count="{{ $post->comments_count }}" x-text="commentCount"></span>
      </button>

      {{-- Spark --}}
      <button @click="toggleSpark()"
              style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:32px;padding:0 10px;border-radius:8px;font:500 12px/1 var(--f-ui);cursor:pointer;transition:all .14s"
              :style="sparked ? 'color:var(--c-saffron)' : 'color:var(--ink-3)'"
              @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
        <svg width="16" height="16" viewBox="0 0 20 20" stroke-linecap="round" stroke-width="1.4"
             :fill="sparked ? 'var(--c-saffron)' : 'none'" :stroke="sparked ? 'var(--c-saffron)' : 'currentColor'">
          <path d="M10 3L11 8L16 9L11 10L10 15L9 10L4 9L9 8z"/>
        </svg>
        <span x-text="sparkCount"></span>
      </button>

      {{-- Share / copy link --}}
      <button style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:32px;padding:0 10px;border-radius:8px;color:var(--ink-3);cursor:pointer;transition:all .14s"
              @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'"
              @click="navigator.clipboard?.writeText('{{ url()->route('posts.show',$post->id) }}').then(()=>{$el.style.color='var(--c-atlas)'}).catch(()=>{})">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
          <path d="M14 4L17 7L14 10M17 7H7a4 4 0 00-4 4v3"/>
        </svg>
      </button>
    </div>

    {{-- ── Inline comments ── --}}
    <div x-show="showComments"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         style="border-top:0.5px solid var(--line);padding-top:12px;display:flex;flex-direction:column;gap:10px">

      {{-- Comments list --}}
      <template x-for="c in comments" :key="c.id ?? c.body">
        <div style="display:grid;grid-template-columns:28px 1fr;gap:8px">
          <div style="width:28px;height:28px;border-radius:999px;background:var(--surface-3);display:flex;align-items:center;justify-content:center;font:600 10px/1 var(--f-ui);color:var(--ink-3);flex-shrink:0"
               x-text="(c.user?.name || '?').split(' ').map(p=>p[0]).join('').toUpperCase().slice(0,2)"></div>
          <div style="background:var(--surface-2);border-radius:0 10px 10px 10px;padding:8px 12px;min-width:0">
            <div style="font:600 12px/1.2 var(--f-ui);color:var(--ink);margin-bottom:3px" x-text="c.user?.name || 'Anonyme'"></div>
            <div style="font:400 12.5px/1.55 var(--f-ui);color:var(--ink-2);white-space:pre-wrap" x-text="c.body"></div>
            <div style="font:400 10px/1 var(--f-mono);color:var(--ink-4);margin-top:5px" x-text="timeAgo(c.created_at)"></div>
          </div>
        </div>
      </template>

      <template x-if="commentsLoaded && comments.length === 0">
        <p style="font:400 12px/1 var(--f-ui);color:var(--ink-3);text-align:center;padding:6px 0;margin:0">
          Soyez le premier à commenter ✦
        </p>
      </template>

      {{-- Comment input --}}
      <div style="display:grid;grid-template-columns:28px 1fr;gap:8px;align-items:center">
        <x-ui.avatar :name="$user->name" size="28"/>
        <div style="display:flex;align-items:center;gap:8px;background:var(--surface-2);border-radius:20px;padding:6px 6px 6px 14px;border:0.5px solid var(--line)">
          <input type="text" x-model="commentText" placeholder="Écrire un commentaire…"
                 @keydown.enter.prevent="submitComment()"
                 style="flex:1;border:0;background:transparent;font:400 13px/1 var(--f-ui);color:var(--ink);outline:none"/>
          <button @click="submitComment()" :disabled="!commentText.trim() || submitting"
                  :style="commentText.trim() && !submitting ? 'background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;cursor:pointer' : 'background:var(--surface-3);color:var(--ink-4);cursor:not-allowed'"
                  style="width:30px;height:30px;border-radius:999px;border:0;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .18s">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
              <path d="M4 10H16M12 6l4 4-4 4"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</article>
