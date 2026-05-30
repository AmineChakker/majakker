@props(['post'])
@php
    $user = auth()->user();
    $liked  = $user->hasReacted($post, 'like');
    $sparked = $user->hasReacted($post, 'spark');
    $roleChip = match($post->user->role) {
        'teacher'  => 'chip-atlas',
        'director' => 'chip-saffron',
        default    => 'chip-blue',
    };
    $roleLabel = match($post->user->role) {
        'teacher'  => 'Professeur',
        'director' => 'Directeur',
        'student'  => 'Étudiant',
        default    => '',
    };
    $pinnedBg = $post->is_pinned
        ? 'linear-gradient(180deg, var(--c-saffron-soft) 0%, transparent 60%)'
        : 'transparent';
@endphp

<article style="padding:var(--row-pad) 18px;border-bottom:0.5px solid var(--line);display:grid;grid-template-columns:32px 1fr;gap:12px;background:{{ $pinnedBg }};transition:background 0.16s;"
         x-data="postCard({{ $post->id }}, {{ (int)$liked }}, {{ $post->likes_count }}, {{ (int)$sparked }}, {{ $post->sparks_count }})"
         @mouseenter="$el.style.background = '{{ $post->is_pinned ? '' : 'rgba(244,239,230,0.45)' }}'"
         @mouseleave="$el.style.background = '{{ $pinnedBg }}'">

    {{-- Avatar --}}
    <x-ui.avatar :name="$post->user->name" size="28"/>

    <div style="display:flex;flex-direction:column;gap:6px;min-width:0">

        {{-- Header row --}}
        <div style="display:flex;align-items:baseline;gap:8px;flex-wrap:wrap">
            <span style="font:600 12.5px/1.2 var(--f-ui)">{{ $post->user->name }}</span>
            @if($roleLabel)
            <span class="chip {{ $roleChip }}">{{ $roleLabel }}</span>
            @endif
            <span style="font:400 11px/1.2 var(--f-ui);color:var(--ink-3)">·</span>
            <span style="font:400 11px/1.2 var(--f-ui);color:var(--ink-3)">{{ $post->where }}</span>
            <span style="font:400 11px/1.2 var(--f-ui);color:var(--ink-3)">·</span>
            <span style="font:400 11px/1.2 var(--f-ui);color:var(--ink-3)">{{ $post->time_ago }}</span>
            @if($post->is_pinned)
            <span style="margin-left:auto;display:inline-flex;align-items:center;gap:4px;color:#8A6520;font:500 10px/1 var(--f-mono);letter-spacing:0.08em;text-transform:uppercase">
                <x-ui.icon name="pin" size="11"/> Épinglé
            </span>
            @endif
        </div>

        {{-- Title --}}
        @if($post->title)
        <div style="font:600 14px/1.35 var(--f-ui);letter-spacing:-0.01em">{{ $post->title }}</div>
        @endif

        {{-- Body --}}
        <div style="font:400 13px/1.55 var(--f-ui);color:var(--ink-2);white-space:pre-wrap">{{ $post->body }}</div>

        {{-- Attachments --}}
        @if($post->attachments->count())
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px">
            @foreach($post->attachments as $att)
                @if($att->kind === 'image')
                    @if($att->path)
                        <img src="{{ $att->url }}" style="border-radius:var(--r-sm);max-width:280px;max-height:180px;object-fit:cover;border:0.5px solid var(--line)"/>
                    @else
                        <div class="ph-img" style="width:220px;height:130px">IMAGE</div>
                    @endif

                @elseif($att->kind === 'file')
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;border:0.5px solid var(--line);background:var(--surface);min-width:200px">
                        <div style="width:28px;height:28px;border-radius:6px;background:var(--c-blue-soft);color:#2A3FB8;display:flex;align-items:center;justify-content:center;font:600 9px/1 var(--f-mono)">{{ $att->ext }}</div>
                        <div style="flex:1;min-width:0">
                            <div style="font:500 11.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $att->original_name }}</div>
                            <div style="font:400 10px/1.2 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $att->file_size_human }}</div>
                        </div>
                    </div>

                @elseif($att->kind === 'poll' && $att->poll)
                    @include('components.feed.poll', ['poll' => $att->poll])
                @endif
            @endforeach
        </div>
        @endif

        {{-- Action bar --}}
        <div style="display:flex;align-items:center;gap:4px;margin-top:4px;margin-left:-6px">

            {{-- Like --}}
            <button @click="toggleLike()"
                    :style="liked ? 'color:var(--c-terracotta)' : 'color:var(--ink-3)'"
                    style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:26px;padding:0 8px;border-radius:7px;font:500 11.5px/1 var(--f-ui);cursor:default;transition:background 0.14s,color 0.14s"
                    @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
                <x-ui.icon name="heart" size="14"/>
                <span x-text="likeCount"></span>
            </button>

            {{-- Comment --}}
            <button style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:26px;padding:0 8px;border-radius:7px;font:500 11.5px/1 var(--f-ui);color:var(--ink-3);cursor:default;transition:background 0.14s"
                    @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
                <x-ui.icon name="cmt" size="14"/>
                <span>{{ $post->comments_count }}</span>
            </button>

            {{-- Spark --}}
            <button @click="toggleSpark()"
                    :style="sparked ? 'color:var(--c-saffron)' : 'color:var(--ink-3)'"
                    style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:26px;padding:0 8px;border-radius:7px;font:500 11.5px/1 var(--f-ui);cursor:default;transition:background 0.14s,color 0.14s"
                    @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
                <x-ui.icon name="spark" size="14"/>
                <span x-text="sparkCount"></span>
            </button>

            {{-- Share --}}
            <button style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:26px;padding:0 8px;border-radius:7px;color:var(--ink-3);cursor:default;transition:background 0.14s"
                    @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
                <x-ui.icon name="share" size="14"/>
            </button>

            <span style="flex:1"></span>

            {{-- Tags --}}
            @foreach($post->hashtags as $tag)
            <span style="font:500 10.5px/1 var(--f-ui);color:var(--ink-3);padding:0 4px">#{{ $tag->name }}</span>
            @endforeach

            {{-- Director: pin toggle --}}
            @if(auth()->user()->isDirector() || auth()->user()->isAdmin())
            <form action="{{ route('posts.pin', $post) }}" method="POST" style="display:inline">
                @csrf @method('POST')
                <button type="submit" style="appearance:none;border:0;background:transparent;height:26px;padding:0 6px;border-radius:7px;color:{{ $post->is_pinned ? '#8A6520' : 'var(--ink-4)' }};cursor:default;transition:background 0.14s"
                        @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background='transparent'">
                    <x-ui.icon name="pin" size="13"/>
                </button>
            </form>
            @endif
        </div>
    </div>
</article>
