<x-layouts.app title="Modifier le profil">
<div style="max-width:640px;margin:40px auto;padding:0 24px">
  <h1 class="serif" style="font:400 32px/1 var(--f-display);margin-bottom:28px">Modifier le profil</h1>
  @if(session('success'))
    <div class="card" style="padding:12px 16px;border-left:3px solid var(--c-atlas);color:var(--c-atlas);margin-bottom:18px">{{ session('success') }}</div>
  @endif

  <div class="card" style="padding:24px;margin-bottom:24px">
    <div style="display:flex;align-items:center;gap:24px">
      <div id="avatar-preview"
     data-has-avatar="{{ $user->avatar_path ? 'true' : 'false' }}"
     data-original-src="{{ $user->avatar_path ? Storage::url($user->avatar_path) : '' }}"
     style="width:80px;height:80px;border-radius:24px;overflow:hidden;background:var(--surface);border:0.5px solid var(--line);box-shadow:var(--sh-md);flex-shrink:0;display:flex;align-items:center;justify-content:center">
        <img id="avatar-img"
     src="{{ $user->avatar_path ? Storage::url($user->avatar_path) : '' }}"
     alt=""
     style="width:100%;height:100%;object-fit:cover;border-radius:24px{{ $user->avatar_path ? '' : ';display:none' }}">
        <span id="avatar-initials" style="{{ $user->avatar_path ? 'display:none' : '' }}">{{ $user->initials }}</span>
      </div>
      <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" x-data="{ showSubmit: false }" style="flex:1">
        @csrf
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
          <label style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:500 13px/1 var(--f-ui);color:var(--ink);cursor:pointer">
            Choisir une photo
            <input id="avatar-input" type="file" name="avatar" accept="image/*" style="display:none" @change="showSubmit = true">
          </label>
          <button type="submit" class="btn btn-primary" x-show="showSubmit">Mettre à jour la photo</button>
        </div>
        @error('avatar')
          <p style="color:var(--c-terracotta);font-size:12px;margin-top:6px">{{ $message }}</p>
        @enderror
      </form>
    </div>
  </div>

  <form action="{{ route('profile.update') }}" method="POST" style="display:flex;flex-direction:column;gap:18px">
    @csrf @method('PATCH')
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

<script>
document.getElementById('avatar-input').addEventListener('change', function () {
  var file = this.files[0];
  var img = document.getElementById('avatar-img');
  var initials = document.getElementById('avatar-initials');
  var preview = document.getElementById('avatar-preview');

  if (file) {
    var reader = new FileReader();
    reader.addEventListener('load', function (e) {
      img.src = e.target.result;
      img.style.display = '';
      initials.style.display = 'none';
    });
    reader.readAsDataURL(file);
  } else {
    if (preview.dataset.hasAvatar === 'true') {
      img.src = preview.dataset.originalSrc;
      img.style.display = '';
      initials.style.display = 'none';
    } else {
      img.style.display = 'none';
      initials.style.display = '';
    }
  }
});
</script>
</x-layouts.app>
