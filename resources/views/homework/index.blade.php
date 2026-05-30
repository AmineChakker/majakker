<x-layouts.app title="Devoirs" subtitle="Mes cours">
<style>
.hw-grid{display:grid;grid-template-columns:1fr 280px;height:100%;overflow:hidden}
.hw-main{overflow:auto;padding:24px 28px 60px}
.hw-rail{overflow:auto;padding:18px 20px;border-left:0.5px solid var(--line);display:flex;flex-direction:column;gap:14px}
.hw-card{background:var(--surface);border:0.5px solid var(--line);border-radius:var(--r-md);padding:16px;display:flex;flex-direction:column;gap:10px;transition:box-shadow .2s}
.hw-card:hover{box-shadow:var(--sh-md)}
</style>

<div class="hw-grid">
  <main class="hw-main scroll">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
      <div>
        <span class="eyebrow">Devoirs &amp; cours</span>
        <h2 class="serif" style="font:400 28px/1.05 var(--f-display);margin:6px 0 0">Mes devoirs</h2>
      </div>
      @if(auth()->user()->isTeacher() || auth()->user()->isDirector())
      <button class="btn btn-primary" x-data @click="document.getElementById('hw-compose-modal').showModal()">
        <x-ui.icon name="plus" size="12"/> Nouveau devoir
      </button>
      @endif
    </div>

    {{-- Homework list --}}
    @forelse($posts as $post)
    <div class="hw-card">
      <div style="display:flex;align-items:flex-start;gap:12px">
        {{-- Class color bar --}}
        @if($post->group)
        <div style="width:4px;min-height:48px;border-radius:2px;background:var(--c-{{ $post->group->color }});flex-shrink:0;margin-top:2px"></div>
        @endif
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px">
            @if($post->group)
            <span class="chip" style="font-size:9.5px;border-left:2px solid var(--c-{{ $post->group->color }})">{{ $post->group->name }}</span>
            @endif
            <span style="font:400 10.5px/1 var(--f-mono);color:var(--ink-3)">{{ $post->created_at->locale('fr')->diffForHumans() }}</span>
            <span style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3)">· {{ $post->user->name }}</span>
          </div>

          @if($post->title)
          <h3 style="font:600 14px/1.3 var(--f-ui);margin:0 0 6px">{{ $post->title }}</h3>
          @endif

          <div style="font:400 13px/1.55 var(--f-ui);color:var(--ink-2);white-space:pre-wrap">{{ Str::limit($post->body, 300) }}</div>

          {{-- Attachments --}}
          @foreach($post->attachments as $att)
          @if($att->kind === 'file')
          <a href="{{ asset('storage/'.$att->path) }}" target="_blank"
             style="display:inline-flex;align-items:center;gap:8px;margin-top:10px;padding:8px 12px;border-radius:8px;background:var(--c-blue-soft);text-decoration:none;color:var(--c-blue);font:500 12px/1 var(--f-ui)">
            <x-ui.icon name="paperclip" size="13"/> {{ $att->original_name }}
            <span style="font:400 10px/1 var(--f-mono);color:var(--ink-3)">{{ round($att->file_size/1024) }} KB</span>
          </a>
          @endif
          @endforeach
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
          <span style="font:500 10px/1 var(--f-mono);color:var(--ink-3)">{{ $post->comments_count }} réponses</span>
        </div>
      </div>
    </div>
    @empty
    <div style="padding:60px;text-align:center;color:var(--ink-3)">
      <x-ui.zellige-star size="40" color="var(--ink-4)" opacity="0.4"/>
      <div style="margin-top:12px;font:400 14px/1.5 var(--f-ui)">Aucun devoir pour le moment.</div>
    </div>
    @endforelse

    @if($posts->hasMorePages())
    <div style="padding:20px;text-align:center">
      <a href="{{ $posts->nextPageUrl() }}" class="btn btn-ghost" style="font-size:12px">Charger plus</a>
    </div>
    @endif
  </main>

  {{-- Rail: class list --}}
  <aside class="hw-rail scroll">
    <span class="eyebrow">Mes classes</span>
    @foreach($groups as $group)
    <a href="{{ route('groups.show', $group) }}"
       style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;background:var(--surface);border:0.5px solid var(--line);text-decoration:none;color:inherit;border-left:3px solid var(--c-{{ $group->color }})">
      <div style="flex:1;min-width:0">
        <div style="font:500 12.5px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $group->name }}</div>
        @if($group->teacher)<div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $group->teacher->name }}</div>@endif
      </div>
      <x-ui.icon name="chevron" size="11" style="color:var(--ink-4)"/>
    </a>
    @endforeach
  </aside>
</div>

{{-- Compose modal (teachers/directors only) --}}
@if(auth()->user()->isTeacher() || auth()->user()->isDirector())
<dialog id="hw-compose-modal" style="border-radius:16px;border:0.5px solid var(--line);padding:28px;width:100%;max-width:520px;box-shadow:var(--sh-lg)">
  <h3 class="serif" style="font:400 24px/1 var(--f-display);margin:0 0 20px">Nouveau devoir</h3>
  <form action="{{ route('posts.store') }}" method="POST" style="display:flex;flex-direction:column;gap:14px">
    @csrf
    <select name="group_id" required style="height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui)">
      <option value="">Sélectionner une classe…</option>
      @foreach($groups as $g)
      <option value="{{ $g->id }}">{{ $g->name }}</option>
      @endforeach
    </select>
    <input name="title" placeholder="Titre du devoir" required style="height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui)"/>
    <textarea name="body" placeholder="Instructions, ressources, date limite…" rows="4" required style="padding:10px 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1.5 var(--f-ui);resize:vertical"></textarea>
    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary">Publier le devoir</button>
      <button type="button" class="btn btn-ghost" onclick="document.getElementById('hw-compose-modal').close()">Annuler</button>
    </div>
  </form>
</dialog>
@endif
</x-layouts.app>
