<x-layouts.app title="Modifier le profil">
<div style="max-width:640px;margin:40px auto;padding:0 24px">
  <h1 class="serif" style="font:400 32px/1 var(--f-display);margin-bottom:28px">Modifier le profil</h1>
  <form action="{{ route('profile.update') }}" method="POST" style="display:flex;flex-direction:column;gap:18px">
    @csrf @method('PATCH')
    @if(session('success'))<div class="card" style="padding:12px 16px;border-left:3px solid var(--c-atlas);color:var(--c-atlas)">{{ session('success') }}</div>@endif
    <div style="display:flex;flex-direction:column;gap:6px">
      <label style="font:500 12.5px/1 var(--f-ui)">Nom complet</label>
      <input name="name" type="text" value="{{ old('name',$user->name) }}" required style="height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui);color:var(--ink);outline:none"/>
      @error('name')<p style="color:var(--c-terracotta);font-size:12px">{{ $message }}</p>@enderror
    </div>
    <div style="display:flex;flex-direction:column;gap:6px">
      <label style="font:500 12.5px/1 var(--f-ui)">Biographie</label>
      <textarea name="bio" rows="3" style="padding:10px 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1.5 var(--f-ui);color:var(--ink);outline:none;resize:vertical">{{ old('bio',$user->bio) }}</textarea>
    </div>
    <div style="display:flex;flex-direction:column;gap:6px">
      <label style="font:500 12.5px/1 var(--f-ui)">Ville</label>
      <input name="location" type="text" value="{{ old('location',$user->location) }}" style="height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui);color:var(--ink);outline:none"/>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">Enregistrer</button>
      <a href="{{ route('profile.show', auth()->user()) }}" class="btn btn-ghost" style="text-decoration:none">Annuler</a>
    </div>
  </form>
</div>
</x-layouts.app>
