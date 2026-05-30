<x-layouts.app title="Recherche" :subtitle="$q ? '« '.$q.' »' : ''">
<div style="max-width:800px;margin:0 auto;padding:28px 24px;height:100%;overflow:auto" class="scroll">
  <form action="{{ route('search') }}" method="GET" style="display:flex;gap:10px;margin-bottom:28px">
    <input name="q" value="{{ $q }}" placeholder="Rechercher posts, utilisateurs, hashtags…"
           style="flex:1;height:44px;padding:0 16px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui);outline:none"/>
    <button type="submit" class="btn btn-primary">Rechercher</button>
  </form>
  @if($q)
    @if($users->count())
    <section style="margin-bottom:28px">
      <div class="eyebrow" style="margin-bottom:12px">Utilisateurs</div>
      @foreach($users as $u)
      <a href="{{ route('profile.show', $u) }}" style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:0.5px solid var(--line);text-decoration:none;color:inherit">
        <x-ui.avatar :name="$u->name" size="36"/>
        <div>
          <div style="font:500 13px/1.2 var(--f-ui)">{{ $u->name }}</div>
          <div style="font:400 11px/1 var(--f-ui);color:var(--ink-3)">{{ $u->role_label }}{{ $u->school ? ' · '.$u->school->name : '' }}</div>
        </div>
      </a>
      @endforeach
    </section>
    @endif
    @if($hashtags->count())
    <section style="margin-bottom:28px">
      <div class="eyebrow" style="margin-bottom:12px">Hashtags</div>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        @foreach($hashtags as $tag)
        <a href="{{ route('search', ['q'=>'#'.$tag->name]) }}" class="chip" style="text-decoration:none">#{{ $tag->name }} <span style="color:var(--ink-4);margin-left:4px">{{ number_format($tag->posts_count) }}</span></a>
        @endforeach
      </div>
    </section>
    @endif
    @if($posts->count())
    <section>
      <div class="eyebrow" style="margin-bottom:12px">Publications</div>
      @foreach($posts as $post)
        <x-feed.post-card :post="$post"/>
      @endforeach
    </section>
    @endif
    @if(!$posts->count() && !$users->count() && !$hashtags->count())
    <div style="padding:60px;text-align:center;color:var(--ink-3)">Aucun résultat pour « {{ $q }} »</div>
    @endif
  @endif
</div>
</x-layouts.app>
