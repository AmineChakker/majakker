{{-- Shared form fields for create/edit user modal --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

  <div style="grid-column:1/-1">
    <label class="adm-label">Nom complet *</label>
    <input name="name" class="adm-input" required placeholder="Yasmine Bennani"
           :value="modal.name" x-model="modal.name"/>
  </div>

  <div>
    <label class="adm-label">Adresse email *</label>
    <input name="email" type="email" class="adm-input" required placeholder="yasmine@majakker.ma"
           :value="modal.email" x-model="modal.email"/>
  </div>

  <div>
    <label class="adm-label">{{ $editing ? 'Nouveau mot de passe' : 'Mot de passe *' }}</label>
    <div style="position:relative">
      <input name="password" type="password" id="modal-pwd"
             class="adm-input" {{ $editing ? '' : 'required' }}
             placeholder="{{ $editing ? 'Laisser vide pour conserver' : '8 caractères minimum' }}"
             style="padding-right:36px"/>
      <button type="button"
              onclick="var i=document.getElementById('modal-pwd');i.type=i.type==='password'?'text':'password'"
              style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:0;cursor:pointer;color:var(--mj-ink-3);display:flex;align-items:center;padding:4px">
        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
          <path d="M2 10 C4 6 7 4 10 4 C13 4 16 6 18 10 C16 14 13 16 10 16 C7 16 4 14 2 10Z"/>
          <circle cx="10" cy="10" r="2.5"/>
        </svg>
      </button>
    </div>
  </div>

  <div>
    <label class="adm-label">Rôle *</label>
    <select name="role" class="adm-input" x-model="modal.role" style="cursor:pointer">
      <option value="admin">Administrateur</option>
      <option value="director">Directeur</option>
      <option value="teacher">Enseignant</option>
      <option value="student">Élève</option>
    </select>
  </div>

  <div x-show="modal.role !== 'admin'">
    <label class="adm-label">École</label>
    <select name="school_id" class="adm-input" x-model="modal.schoolId" style="cursor:pointer">
      <option value="">— Sélectionner une école</option>
      @foreach($allSchools as $s)
      <option value="{{ $s->id }}">{{ $s->name }} · {{ $s->city }}</option>
      @endforeach
    </select>
  </div>

  <div style="grid-column:1/-1;display:flex;align-items:center;gap:8px">
    <input type="hidden" name="is_active" value="0"/>
    <input type="checkbox" name="is_active" value="1" id="modal-active"
           :checked="modal.isActive" x-model="modal.isActive"
           style="width:15px;height:15px;accent-color:#7E5BEF;cursor:pointer"/>
    <label for="modal-active" style="font:500 12.5px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">Compte actif</label>
  </div>

</div>
