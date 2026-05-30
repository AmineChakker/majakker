<x-layouts.admin title="Écoles" :subtitle="$schools->total().' partenaires'">
<div style="padding:28px 32px 64px;height:100%;overflow:auto" class="adm-scroll">

  {{-- ── Credential banner shown once after school creation ── --}}
  @if(session('created_school'))
  <div style="margin-bottom:24px;padding:20px 24px;border-radius:14px;background:linear-gradient(135deg,rgba(126,91,239,.10),rgba(37,99,235,.08));border:0.5px solid rgba(126,91,239,.25);display:flex;gap:20px;align-items:flex-start">
    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
    </div>
    <div style="flex:1;min-width:0">
      <div style="font:600 14px/1.3 var(--f-ui);color:var(--mj-ink);margin-bottom:6px">
        École « {{ session('created_school') }} » créée avec succès.
      </div>
      <div style="font:400 13px/1.5 var(--f-ui);color:var(--mj-ink-2);margin-bottom:12px">
        Transmettez ces identifiants au directeur de l'école. Ils ne seront affichés qu'une seule fois.
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <div style="display:flex;flex-direction:column;gap:4px;padding:10px 14px;border-radius:10px;background:rgba(255,255,255,.7);border:0.5px solid rgba(126,91,239,.2)">
          <span style="font:500 9.5px/1 var(--f-mono);letter-spacing:.14em;text-transform:uppercase;color:var(--mj-ink-3)">Email</span>
          <span style="font:500 14px/1 var(--f-mono);color:var(--mj-ink)">{{ session('created_director_email') }}</span>
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;padding:10px 14px;border-radius:10px;background:rgba(255,255,255,.7);border:0.5px solid rgba(126,91,239,.2)">
          <span style="font:500 9.5px/1 var(--f-mono);letter-spacing:.14em;text-transform:uppercase;color:var(--mj-ink-3)">Mot de passe</span>
          <span style="font:500 14px/1 var(--f-mono);color:var(--mj-ink)">{{ session('created_director_password') }}</span>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if(session('success'))
  <div style="margin-bottom:20px;padding:14px 18px;border-radius:10px;background:rgba(16,185,129,.08);border:0.5px solid rgba(16,185,129,.25);color:#047857;font:500 13px/1 var(--f-ui)">
    {{ session('success') }}
  </div>
  @endif

  {{-- ── Header ── --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
      <span class="adm-eyebrow">Gestion des écoles</span>
      <h2 style="font:400 32px/1.05 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">Écoles partenaires</h2>
    </div>
    <button onclick="document.getElementById('modal-create').showModal()" class="adm-cta">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      Nouvelle école
    </button>
  </div>

  {{-- ── Table ── --}}
  <div style="background:var(--mj-surface);border:0.5px solid var(--mj-line);border-radius:16px;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="border-bottom:0.5px solid var(--mj-line)">
          @foreach(['École','Ville','Directeur','Élèves','Publications','Plan','Statut','Inscrite le','Actions'] as $h)
          <th style="text-align:left;padding:12px 16px;font:500 10px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--mj-ink-3);white-space:nowrap">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($schools as $s)
        <tr style="border-bottom:0.5px solid var(--mj-line);transition:background .15s" onmouseenter="this.style.background='var(--surface-2)'" onmouseleave="this.style.background=''">

          {{-- School name + initial --}}
          <td style="padding:14px 16px">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:32px;height:32px;border-radius:9px;background:var(--mj-gradient-soft);color:var(--mj-purple);display:flex;align-items:center;justify-content:center;font:600 13px/1 var(--f-ui);flex-shrink:0">
                {{ $s->initial }}
              </div>
              <div>
                <div style="font:600 13px/1.2 var(--f-ui)">{{ $s->name }}</div>
                <div style="font:400 10.5px/1 var(--f-mono);color:var(--mj-ink-3);margin-top:2px">{{ $s->users_count }} membres</div>
              </div>
            </div>
          </td>

          {{-- City --}}
          <td style="padding:14px 16px;font:400 12.5px/1 var(--f-ui);color:var(--mj-ink-2)">{{ $s->city }}</td>

          {{-- Director --}}
          <td style="padding:14px 16px">
            @if($s->director)
            <div style="font:500 12px/1.2 var(--f-ui)">{{ $s->director->name }}</div>
            <div style="font:400 10.5px/1 var(--f-ui);color:var(--mj-ink-3);margin-top:2px">{{ $s->director->email }}</div>
            @else
            <span style="font:400 11px/1 var(--f-ui);color:var(--mj-ink-3)">—</span>
            @endif
          </td>

          {{-- Students --}}
          <td style="padding:14px 16px;font:500 12.5px/1 var(--f-mono)">{{ number_format($s->student_count) }}</td>

          {{-- Posts --}}
          <td style="padding:14px 16px;font:500 12.5px/1 var(--f-mono)">{{ number_format($s->posts_count) }}</td>

          {{-- Plan --}}
          <td style="padding:14px 16px">
            @php
              $tierClass = match($s->plan) { 'pro' => 'pro', 'school' => 'school', default => 'starter' };
              $tierLabel = match($s->plan) { 'pro' => 'PRO', 'school' => 'SCHOOL', default => 'STARTER' };
            @endphp
            <span class="adm-tier {{ $tierClass }}">{{ $tierLabel }}</span>
          </td>

          {{-- Status --}}
          <td style="padding:14px 16px">
            <span style="display:inline-flex;align-items:center;gap:5px;height:20px;padding:0 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);
              {{ $s->is_active ? 'background:rgba(16,185,129,.10);color:#047857' : 'background:rgba(220,38,38,.08);color:#B91C1C' }}">
              <span style="width:5px;height:5px;border-radius:999px;background:currentColor"></span>
              {{ $s->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>

          {{-- Date --}}
          <td style="padding:14px 16px;font:400 11.5px/1 var(--f-mono);color:var(--mj-ink-3);white-space:nowrap">{{ $s->created_at->format('d M Y') }}</td>

          {{-- Actions --}}
          <td style="padding:14px 16px">
            <div style="display:flex;align-items:center;gap:6px">
              <a href="{{ route('admin.schools.edit', $s) }}"
                 style="height:30px;padding:0 12px;border-radius:8px;border:0.5px solid var(--mj-line-2);background:var(--mj-surface);font:500 11px/30px var(--f-ui);color:var(--mj-ink-2);text-decoration:none;display:inline-block;white-space:nowrap">
                Modifier
              </a>
              <form action="{{ route('admin.schools.destroy', $s) }}" method="POST"
                    onsubmit="return confirm('Supprimer « {{ addslashes($s->name) }} » ? Les utilisateurs seront détachés mais conservés.')">
                @csrf @method('DELETE')
                <button type="submit" style="height:30px;padding:0 12px;border-radius:8px;border:0.5px solid rgba(220,38,38,.25);background:rgba(220,38,38,.05);font:500 11px/1 var(--f-ui);color:#B91C1C;cursor:pointer;white-space:nowrap">
                  Supprimer
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" style="padding:60px;text-align:center;color:var(--mj-ink-3)">
            Aucune école enregistrée
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:20px">{{ $schools->links() }}</div>
</div>

{{-- ══ CREATE SCHOOL MODAL ══ --}}
<dialog id="modal-create" style="border-radius:18px;border:0.5px solid var(--mj-line);padding:0;width:100%;max-width:580px;box-shadow:0 32px 80px -20px rgba(20,21,43,.25);background:var(--mj-surface)">
  <div style="padding:28px 28px 0;border-bottom:0.5px solid var(--mj-line);margin-bottom:0">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
      <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      </div>
      <div>
        <h3 style="font:400 22px/1 var(--f-display);margin:0">Nouvelle école</h3>
        <p style="font:400 12px/1 var(--f-ui);color:var(--mj-ink-3);margin:4px 0 0">Crée l'école et le compte directeur en une seule étape.</p>
      </div>
      <button onclick="document.getElementById('modal-create').close()" style="margin-left:auto;width:28px;height:28px;border-radius:7px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--mj-ink-3)">
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 4l12 12M16 4L4 16"/></svg>
      </button>
    </div>
  </div>

  <form action="{{ route('admin.schools.store') }}" method="POST" style="padding:24px 28px 28px;display:flex;flex-direction:column;gap:0">
    @csrf

    {{-- Validation errors --}}
    @if($errors->any())
    <div style="padding:12px 14px;border-radius:10px;background:rgba(220,38,38,.07);border:0.5px solid rgba(220,38,38,.2);color:#B91C1C;font:400 12.5px/1.5 var(--f-ui);margin-bottom:20px">
      {{ $errors->first() }}
    </div>
    @endif

    {{-- Section: School info --}}
    <div style="margin-bottom:20px">
      <div style="font:500 10px/1 var(--f-mono);letter-spacing:.18em;text-transform:uppercase;color:var(--mj-ink-3);margin-bottom:14px;padding-bottom:8px;border-bottom:0.5px solid var(--mj-line)">
        Informations de l'école
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div style="grid-column:1/-1">
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Nom de l'école *</label>
          <input name="name" value="{{ old('name') }}" required placeholder="ex. Lycée Majakker"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Ville *</label>
          <input name="city" value="{{ old('city') }}" required placeholder="Casablanca"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Nombre d'élèves</label>
          <input name="student_count" type="number" min="0" value="{{ old('student_count',0) }}" placeholder="0"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div style="grid-column:1/-1">
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:8px">Plan *</label>
          <div style="display:flex;gap:8px">
            @foreach(['starter'=>['STARTER','rgba(20,21,43,.06)','var(--mj-ink-2)'],'school'=>['SCHOOL','rgba(6,182,212,.10)','#0E7490'],'pro'=>['PRO','linear-gradient(135deg,#7E5BEF,#2563EB)','#fff']] as $val=>[$lbl,$bg,$fg])
            <label style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;padding:12px;border-radius:10px;border:0.5px solid var(--mj-line-2);cursor:pointer;transition:border-color .15s" x-data
                   onclick="this.parentElement.querySelectorAll('label').forEach(l=>l.style.borderColor='var(--mj-line-2)');this.style.borderColor='#7E5BEF'">
              <input type="radio" name="plan" value="{{ $val }}" {{ old('plan','starter')===$val?'checked':'' }} style="display:none"/>
              <span style="display:inline-flex;align-items:center;height:20px;padding:0 10px;border-radius:999px;background:{{ $bg }};color:{{ $fg }};font:600 10px/1 var(--f-mono);letter-spacing:.1em">{{ $lbl }}</span>
              <span style="font:400 10.5px/1.3 var(--f-ui);color:var(--mj-ink-3);text-align:center">
                @if($val==='starter') Gratuit · jusqu'à 200 élèves
                @elseif($val==='school') 1 100 DH/mois
                @else 2 200 DH/mois
                @endif
              </span>
            </label>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Section: Director account --}}
    <div style="margin-bottom:24px">
      <div style="font:500 10px/1 var(--f-mono);letter-spacing:.18em;text-transform:uppercase;color:var(--mj-ink-3);margin-bottom:14px;padding-bottom:8px;border-bottom:0.5px solid var(--mj-line)">
        Compte du directeur
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div style="grid-column:1/-1">
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Nom complet *</label>
          <input name="director_name" value="{{ old('director_name') }}" required placeholder="Najat Tazi"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Adresse email *</label>
          <input name="director_email" type="email" value="{{ old('director_email') }}" required placeholder="directeur@ecole.ma"
                 style="width:100%;height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                 onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                 onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
        </div>
        <div>
          <label style="font:500 12px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px">Mot de passe *</label>
          <div style="position:relative">
            <input name="director_password" type="password" id="create-pwd" required placeholder="8 caractères minimum"
                   style="width:100%;height:42px;padding:0 40px 0 14px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box"
                   onfocus="this.style.borderColor='#7E5BEF';this.style.boxShadow='0 0 0 3px rgba(126,91,239,.12)'"
                   onblur="this.style.borderColor='var(--mj-line-2)';this.style.boxShadow='none'"/>
            <button type="button" onclick="var i=document.getElementById('create-pwd');i.type=i.type==='password'?'text':'password'"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:0;cursor:pointer;color:var(--mj-ink-3);display:flex;align-items:center;padding:4px">
              <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M2 10 C4 6 7 4 10 4 C13 4 16 6 18 10 C16 14 13 16 10 16 C7 16 4 14 2 10Z"/><circle cx="10" cy="10" r="2.5"/></svg>
            </button>
          </div>
          <div style="margin-top:5px;text-align:right">
            <button type="button" onclick="var p=Math.random().toString(36).slice(2,10)+'!A1';document.getElementById('create-pwd').value=p;document.getElementById('create-pwd').type='text'"
                    style="border:0;background:0;cursor:pointer;font:500 10.5px/1 var(--f-ui);color:#7E5BEF;padding:0">
              Générer automatiquement
            </button>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button type="submit" class="adm-cta" style="flex:1;justify-content:center">
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
        Créer l'école et le compte directeur
      </button>
      <button type="button" onclick="document.getElementById('modal-create').close()"
              style="height:42px;padding:0 18px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:500 13px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">
        Annuler
      </button>
    </div>
  </form>
</dialog>

{{-- Auto-open create modal if there were validation errors --}}
@if($errors->any())
<script>document.addEventListener('DOMContentLoaded',()=>document.getElementById('modal-create').showModal())</script>
@endif
</x-layouts.admin>
