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
        {{-- Context menu (always shown; options differ by role) --}}
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
          <div x-show="open" x-cloak @click="open=false"
               style="position:absolute;right:0;top:28px;background:var(--surface);border:0.5px solid var(--line-2);
                      border-radius:10px;box-shadow:var(--sh-lg);min-width:164px;overflow:hidden;z-index:20">

            {{-- Pin (director / admin only) --}}
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

            {{-- Delete (author OR director/admin) --}}
            @if($me->id===$post->user_id || $me->isDirector() || $me->isAdmin())
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
            @endif

            {{-- Report (anyone except the author and moderators) --}}
            @if($me->id!==$post->user_id && !$me->isDirector() && !$me->isAdmin())
            @if($me->isDirector() || $me->isAdmin())<div style="height:0.5px;background:var(--line);margin:2px 0"></div>@endif
            <button type="button"
                    @click="showReport=true"
                    style="width:100%;padding:9px 14px;border:0;background:0;text-align:left;
                           font:400 12.5px/1 var(--f-ui);color:var(--ink-2);cursor:pointer;
                           display:flex;align-items:center;gap:8px"
                    @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background=''">
              <x-ui.icon name="flag" size="12" style="color:var(--ink-3)"/>
              Signaler
            </button>
            @endif
          </div>
        </div>
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

  {{-- ── Report modal (teleported to <body> so position:fixed escapes any ancestor transform/stacking context) ── --}}
  @if($me->id!==$post->user_id && !$me->isDirector() && !$me->isAdmin())
  <template x-teleport="body">
  <div x-show="showReport" x-cloak
       style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px"
       @keydown.escape.window="showReport=false">
    <div style="position:absolute;inset:0;background:rgba(20,21,43,.45);backdrop-filter:blur(5px)"
         @click="showReport=false"></div>
    <div style="position:relative;z-index:1;width:100%;max-width:420px;background:var(--surface);border-radius:16px;
                border:0.5px solid var(--line);box-shadow:0 32px 80px -20px rgba(20,21,43,.38);overflow:hidden"
         @click.stop>

      {{-- Header --}}
      <div style="display:flex;align-items:center;gap:12px;padding:18px 20px;border-bottom:0.5px solid var(--line)">
        <div style="width:32px;height:32px;border-radius:9px;background:rgba(220,38,38,.10);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="flag" size="14" style="color:#B91C1C"/>
        </div>
        <div>
          <div style="font:600 14px/1.2 var(--f-ui);color:var(--ink)">Signaler cette publication</div>
          <div style="font:400 11.5px/1.2 var(--f-ui);color:var(--ink-3);margin-top:2px">Le directeur examinera votre signalement.</div>
        </div>
        <button @click="showReport=false"
                style="margin-left:auto;width:26px;height:26px;border-radius:7px;border:0.5px solid var(--line-2);
                       background:var(--surface-2);cursor:pointer;display:flex;align-items:center;
                       justify-content:center;color:var(--ink-3);flex-shrink:0">
          <svg width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
            <path d="M4 4l12 12M16 4L4 16"/>
          </svg>
        </button>
      </div>

      {{-- Body --}}
      <div style="padding:18px 20px;display:flex;flex-direction:column;gap:12px">

        {{-- Success state --}}
        <template x-if="reportSent">
          <div style="display:flex;flex-direction:column;align-items:center;gap:12px;padding:20px 0;text-align:center">
            <div style="width:48px;height:48px;border-radius:999px;background:rgba(16,185,129,.12);
                        display:flex;align-items:center;justify-content:center">
              <svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="#047857" stroke-width="1.6" stroke-linecap="round">
                <path d="M4 10l5 5L16 6"/>
              </svg>
            </div>
            <div>
              <div style="font:600 14px/1.3 var(--f-ui);color:var(--ink);margin-bottom:4px">Signalement envoyé</div>
              <div style="font:400 12.5px/1.5 var(--f-ui);color:var(--ink-3)">Merci de nous aider à maintenir un espace sûr.</div>
            </div>
          </div>
        </template>

        {{-- Form --}}
        <template x-if="!reportSent">
          <div style="display:flex;flex-direction:column;gap:12px">
            <div style="font:500 12px/1 var(--f-ui);color:var(--ink-2)">Motif du signalement *</div>
            @foreach([
              ['Contenu inapproprié ou offensant',     'inappropriate'],
              ['Harcèlement ou intimidation',           'harassment'],
              ['Fausses informations',                  'misinformation'],
              ['Spam ou publicité',                     'spam'],
              ['Autre',                                 'other'],
            ] as [$label, $value])
            <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;
                          border:0.5px solid var(--line-2);cursor:pointer;transition:all .14s"
                   :style="reportReason==='{{ $value }}' ? 'border-color:#7E5BEF;background:rgba(126,91,239,.06)' : ''"
                   @mouseenter="reportReason!=='{{ $value }}' && ($el.style.background='var(--surface-2)')"
                   @mouseleave="reportReason!=='{{ $value }}' && ($el.style.background='')">
              <input type="radio" name="report_reason_{{ $post->id }}" value="{{ $value }}"
                     x-model="reportReason"
                     style="accent-color:#7E5BEF;width:15px;height:15px;flex-shrink:0"/>
              <span style="font:400 13px/1.3 var(--f-ui);color:var(--ink-2)">{{ $label }}</span>
            </label>
            @endforeach

            {{-- Optional detail --}}
            <div>
              <label style="font:500 11.5px/1 var(--f-ui);color:var(--ink-3);display:block;margin-bottom:6px">Précisions (optionnel)</label>
              <textarea x-model="reportDetail" rows="2" placeholder="Décrivez le problème…"
                        style="width:100%;padding:9px 12px;border-radius:9px;border:0.5px solid var(--line-2);
                               background:var(--surface-2);font:400 13px/1.5 var(--f-ui);color:var(--ink);
                               outline:none;resize:none;box-sizing:border-box;transition:border-color .16s"
                        @focus="$el.style.borderColor='#7E5BEF'" @blur="$el.style.borderColor='var(--line-2)'"></textarea>
            </div>

            {{-- Error --}}
            <div x-show="reportError" x-cloak
                 style="padding:9px 12px;border-radius:9px;background:rgba(220,38,38,.07);
                        border:0.5px solid rgba(220,38,38,.2);color:#B91C1C;font:400 12.5px/1.4 var(--f-ui)"
                 x-text="reportError"></div>

            {{-- Actions --}}
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="button" @click="showReport=false"
                      style="flex:1;height:38px;border-radius:9px;border:0.5px solid var(--line-2);
                             background:var(--surface-2);font:500 13px/1 var(--f-ui);color:var(--ink-2);cursor:pointer">
                Annuler
              </button>
              <button type="button" @click="submitReport()"
                      :disabled="!reportReason || reporting"
                      :style="reportReason && !reporting
                        ? 'opacity:1;cursor:pointer;background:linear-gradient(135deg,#7E5BEF,#2563EB)'
                        : 'opacity:0.45;cursor:not-allowed;background:var(--surface-3)'"
                      style="flex:1;height:38px;border-radius:9px;border:0;color:#fff;
                             font:600 13px/1 var(--f-ui);transition:opacity .18s;display:inline-flex;align-items:center;justify-content:center;gap:7px">
                <template x-if="reporting">
                  <svg width="13" height="13" viewBox="0 0 30 30" fill="none" stroke="currentColor" stroke-width="2.5"
                       stroke-linecap="round" style="animation:spin 1s linear infinite">
                    <circle cx="15" cy="15" r="12" stroke-opacity=".3"/><path d="M15 3 a12 12 0 0 1 12 12"/>
                  </svg>
                </template>
                <x-ui.icon name="flag" size="12"/>
                Signaler
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
  </template>
  @endif

</article>
