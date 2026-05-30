<x-layouts.app :title="'Profil'" :subtitle="'@'.$user->handle">
<style>
.profile-hero{position:relative;padding:32px 36px 24px;background:linear-gradient(180deg,var(--surface-2) 0%,var(--bg) 100%);border-bottom:0.5px solid var(--line);overflow:hidden}
.profile-body{display:grid;grid-template-columns:1fr 280px;gap:24px;padding:20px 36px 60px}
</style>

<div style="height:100%;overflow:auto" class="scroll">
  {{-- Hero --}}
  <div class="profile-hero">
    <div class="zellige" style="position:absolute;inset:0;opacity:.06;pointer-events:none"></div>
    <div style="position:relative;display:flex;gap:24px;align-items:flex-end">
      <div style="width:96px;height:96px;border-radius:24px;background:var(--surface);border:0.5px solid var(--line);box-shadow:var(--sh-md);display:flex;align-items:center;justify-content:center;font:400 42px/1 var(--f-display);color:var(--ink)">
        {{ $user->initials }}
      </div>
      <div style="flex:1">
        <span class="eyebrow">{{ $user->role_label }}{{ $user->school ? ' · '.$user->school->name : '' }}</span>
        <h1 class="serif" style="font:400 40px/1.05 var(--f-display);margin:6px 0 4px;letter-spacing:-.02em">{{ $user->name }}</h1>
        <div style="display:flex;gap:14px;font:400 11.5px/1.4 var(--f-ui);color:var(--ink-3)">
          @if($user->location)<span>{{ $user->location }}</span><span>·</span>@endif
          <span>Membre depuis {{ $user->joined_at?->year ?? date('Y') }}</span>
          <span>·</span>
          <span>@{{ $user->handle }}</span>
        </div>
      </div>
      @if(auth()->id() !== $user->id)
      <div style="display:flex;gap:8px">
        <a href="{{ route('messages.show', $user) }}" class="btn" style="text-decoration:none"><x-ui.icon name="msg" size="12"/> Message</a>
        <button class="btn btn-primary">Suivre</button>
      </div>
      @else
      <a href="{{ route('profile.edit') }}" class="btn" style="text-decoration:none">Modifier le profil</a>
      @endif
    </div>

    @if($user->bio)
    <div style="position:relative;margin-top:20px;max-width:640px;font:400 14px/1.55 var(--f-ui);color:var(--ink-2)">{{ $user->bio }}</div>
    @endif

    <div style="position:relative;display:flex;gap:32px;margin-top:22px">
      @foreach([['Publications',$user->posts()->count()],['Clubs',$user->groups()->where('kind','club')->count()],['Cours',$user->groups()->where('kind','class')->count()]] as [$lbl,$n])
      <div style="display:flex;flex-direction:column;gap:2px">
        <span style="font:400 22px/1 var(--f-display)">{{ number_format($n) }}</span>
        <span class="eyebrow" style="font-size:9.5px">{{ $lbl }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Tabs --}}
  <div style="padding:0 36px;border-bottom:0.5px solid var(--line);display:flex;gap:4px">
    @foreach(['Publications','Médias','Réussites','Clubs','À propos'] as $i => $tab)
    <div style="padding:12px;font:500 12.5px/1 var(--f-ui);color:{{ $i===0?'var(--ink)':'var(--ink-3)' }};border-bottom:{{ $i===0?'1.5px solid var(--ink)':'1.5px solid transparent' }};margin-bottom:-0.5px">{{ $tab }}</div>
    @endforeach
  </div>

  {{-- Body --}}
  <div class="profile-body">
    <div style="display:flex;flex-direction:column">
      @forelse($posts as $post)
        <x-feed.post-card :post="$post"/>
      @empty
        <div style="padding:40px;text-align:center;color:var(--ink-3)">Aucune publication</div>
      @endforelse
    </div>

    <aside style="display:flex;flex-direction:column;gap:18px">
      @if($user->badges->count())
      <section style="display:flex;flex-direction:column;gap:8px">
        <span class="eyebrow">Réussites</span>
        @foreach($user->badges as $i => $badge)
        @php $colors = ['saffron','blue','atlas','terracotta']; $c = $colors[$i%4]; @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;background:var(--surface);border:0.5px solid var(--line)">
          <div style="width:26px;height:26px;border-radius:7px;background:var(--c-{{ $c }}-soft);color:var(--c-{{ $c }});display:flex;align-items:center;justify-content:center">
            <x-ui.icon name="award" size="14"/>
          </div>
          <span style="font:500 11.5px/1.3 var(--f-ui)">{{ $badge->label }}</span>
        </div>
        @endforeach
      </section>
      @endif
    </aside>
  </div>
</div>
</x-layouts.app>
