<x-layouts.admin :title="'Modifier · '.$school->name" subtitle="Paramètres de l'école">
<div style="padding:28px 32px 64px;height:100%;overflow:auto;max-width:720px" class="adm-scroll">

  @if(session('success'))
  <div style="margin-bottom:20px;padding:14px 18px;border-radius:10px;background:rgba(16,185,129,.08);border:0.5px solid rgba(16,185,129,.25);color:#047857;font:500 13px/1 var(--f-ui)">
    {{ session('success') }}
  </div>
  @endif

  @if($errors->any())
  <div style="margin-bottom:20px;padding:14px 18px;border-radius:10px;background:rgba(220,38,38,.07);border:0.5px solid rgba(220,38,38,.2);color:#B91C1C;font:400 13px/1.5 var(--f-ui)">
    {{ $errors->first() }}
  </div>
  @endif

  <div style="display:flex;align-items:center;gap:14px;margin-bottom:28px">
    <div style="width:48px;height:48px;border-radius:13px;background:var(--mj-gradient);color:#fff;display:flex;align-items:center;justify-content:center;font:600 18px/1 var(--f-ui)">
      {{ $school->initial }}
    </div>
    <div>
      <h2 style="font:400 28px/1.05 var(--f-display);margin:0;letter-spacing:-.02em">{{ $school->name }}</h2>
      <div style="font:400 11.5px/1 var(--f-mono);color:var(--mj-ink-3);margin-top:4px">Inscrite le {{ $school->created_at->format('d M Y') }}</div>
    </div>
    <a href="{{ route('admin.schools') }}" style="margin-left:auto;height:36px;padding:0 16px;border-radius:9px;border:0.5px solid var(--mj-line-2);background:var(--mj-surface);font:500 12px/36px var(--f-ui);color:var(--mj-ink-2);text-decoration:none;display:inline-block">
      ← Retour
    </a>
  </div>

  <form action="{{ route('admin.schools.update', $school) }}" method="POST" style="display:flex;flex-direction:column;gap:24px">
    @csrf @method('PATCH')

    {{-- School info --}}
    <div style="background:var(--mj-surface);border:0.5px solid var(--mj-line);border-radius:14px;padding:22px;display:flex;flex-direction:column;gap:16px">
      <div style="font:500 10px/1 var(--f-mono);letter-spacing:.18em;text-transform:uppercase;color:var(--mj-ink-3);padding-bottom:10px;border-bottom:0.5px solid var(--mj-line)">
        Informations de l'école
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div style="grid-column:1/-1">
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Nom de l'école *</label>
          <input name="name" value="{{ old('name',$school->name) }}" required
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Ville *</label>
          <input name="city" value="{{ old('city',$school->city) }}" required
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Nombre d'élèves</label>
          <input name="student_count" type="number" min="0" value="{{ old('student_count',$school->student_count) }}"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:8px">Plan *</label>
          <select name="plan" style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box">
            @foreach(['starter'=>'Starter — Gratuit','school'=>'School — 1 100 DH/mois','pro'=>'Pro — 2 200 DH/mois'] as $val=>$lbl)
            <option value="{{ $val }}" {{ old('plan',$school->plan)===$val?'selected':'' }}>{{ $lbl }}</option>
            @endforeach
          </select>
        </div>
        <div style="grid-column:1/-1;display:flex;align-items:center;gap:10px">
          <input type="hidden" name="is_active" value="0"/>
          <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active',$school->is_active)?'checked':'' }}
                 style="width:16px;height:16px;accent-color:#7E5BEF;cursor:pointer"/>
          <label for="is_active" style="font:500 13px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">École active</label>
        </div>
      </div>
    </div>

    {{-- Director account --}}
    <div style="background:var(--mj-surface);border:0.5px solid var(--mj-line);border-radius:14px;padding:22px;display:flex;flex-direction:column;gap:16px">
      <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:10px;border-bottom:0.5px solid var(--mj-line)">
        <span style="font:500 10px/1 var(--f-mono);letter-spacing:.18em;text-transform:uppercase;color:var(--mj-ink-3)">Compte du directeur</span>
        @if($school->director)
        <span style="display:inline-flex;align-items:center;gap:5px;height:18px;padding:0 8px;border-radius:999px;background:rgba(16,185,129,.10);color:#047857;font:600 9.5px/1 var(--f-mono)">
          <span style="width:5px;height:5px;border-radius:999px;background:currentColor"></span>Compte existant
        </span>
        @else
        <span style="font:400 11px/1 var(--f-ui);color:var(--mj-ink-3)">Aucun directeur — remplissez les champs pour en créer un</span>
        @endif
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div style="grid-column:1/-1">
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Nom complet</label>
          <input name="director_name" value="{{ old('director_name',$school->director?->name) }}" placeholder="Laisser vide pour ne pas modifier"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Adresse email</label>
          <input name="director_email" type="email" value="{{ old('director_email',$school->director?->email) }}" placeholder="Laisser vide pour ne pas modifier"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Nouveau mot de passe</label>
          <div style="position:relative">
            <input name="director_password" type="password" id="edit-pwd" placeholder="Laisser vide pour conserver l'actuel"
                   style="width:100%;height:42px;padding:0 40px 0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                   onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                   onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
            <button type="button" onclick="var i=document.getElementById('edit-pwd');i.type=i.type==='password'?'text':'password'"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:0;cursor:pointer;color:var(--mj-ink-3);display:flex;align-items:center;padding:4px">
              <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M2 10 C4 6 7 4 10 4 C13 4 16 6 18 10 C16 14 13 16 10 16 C7 16 4 14 2 10Z"/><circle cx="10" cy="10" r="2.5"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;gap:8px">
      <button type="submit" class="adm-cta">
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
        Enregistrer les modifications
      </button>
      <a href="{{ route('admin.schools') }}"
         style="height:42px;padding:0 18px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:500 13px/42px var(--f-ui);color:var(--mj-ink-2);text-decoration:none;display:inline-block">
        Annuler
      </a>
      <form action="{{ route('admin.schools.destroy', $school) }}" method="POST" style="margin-left:auto"
            onsubmit="return confirm('Supprimer définitivement « {{ addslashes($school->name) }} » ?')">
        @csrf @method('DELETE')
        <button type="submit"
                style="height:42px;padding:0 18px;border-radius:10px;border:0.5px solid rgba(220,38,38,.25);background:rgba(220,38,38,.05);font:500 13px/1 var(--f-ui);color:#B91C1C;cursor:pointer">
          Supprimer l'école
        </button>
      </form>
    </div>
  </form>
</div>
</x-layouts.admin>
