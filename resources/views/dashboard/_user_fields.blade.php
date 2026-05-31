{{-- Shared fields for teacher & student create/edit modals --}}
{{-- Props: $showClasses (bool), $singleClass (bool for student) --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
  <div style="grid-column:1/-1">
    <label class="dir-label">Nom complet *</label>
    <input name="name" class="dir-input" required placeholder="Karim El Idrissi" x-model="modal.name"/>
  </div>
  <div>
    <label class="dir-label">Adresse email *</label>
    <input name="email" type="email" class="dir-input" required placeholder="k.elidrissi@ecole.ma" x-model="modal.email"/>
  </div>
  <div>
    <label class="dir-label" x-text="modal.editing ? 'Nouveau mot de passe' : 'Mot de passe *'"></label>
    <div style="position:relative">
      <input name="password" type="password" id="dir-pwd" class="dir-input" :required="!modal.editing"
             placeholder="8 caractères minimum" x-model="modal.password" style="padding-right:38px"/>
      <button type="button" onclick="var i=document.getElementById('dir-pwd');i.type=i.type==='password'?'text':'password'"
              style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:0;cursor:pointer;color:var(--ink-3);display:flex;align-items:center;padding:4px">
        <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
          <path d="M2 10 C4 6 7 4 10 4 C13 4 16 6 18 10 C16 14 13 16 10 16 C7 16 4 14 2 10Z"/>
          <circle cx="10" cy="10" r="2.5"/>
        </svg>
      </button>
    </div>
    @if(isset($showClasses) && $showClasses)
    <div style="margin-top:4px;text-align:right">
      <button type="button" onclick="var p=Math.random().toString(36).slice(2,8)+'!A1b';var i=document.getElementById('dir-pwd');i.value=p;i.type='text'"
              style="border:0;background:0;cursor:pointer;font:500 10.5px/1 var(--f-ui);color:#7E5BEF;padding:0">Générer</button>
    </div>
    @endif
  </div>

  @if(isset($showClasses) && $showClasses)
  {{-- Multi-select classes for teacher --}}
  <div style="grid-column:1/-1">
    <label class="dir-label">Classes assignées</label>
    <div style="display:flex;flex-direction:column;gap:5px;max-height:140px;overflow:auto;padding:8px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2)">
      @foreach($classes as $cls)
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:3px 0">
        <input type="checkbox" name="class_ids[]" value="{{ $cls->id }}"
               :checked="modal.classIds.includes({{ $cls->id }})"
               style="accent-color:#7E5BEF;width:14px;height:14px;cursor:pointer"/>
        <span style="font:400 12.5px/1 var(--f-ui);color:var(--ink-2)">{{ $cls->name }}</span>
        <span style="width:6px;height:6px;border-radius:999px;background:var(--c-{{ $cls->color }});margin-left:auto;flex-shrink:0"></span>
      </label>
      @endforeach
      @if($classes->isEmpty())<div style="font:400 12px/1 var(--f-ui);color:var(--ink-3)">Aucune classe disponible</div>@endif
    </div>
  </div>
  @elseif(isset($singleClass) && $singleClass)
  {{-- Single class for student --}}
  <div style="grid-column:1/-1">
    <label class="dir-label">Classe</label>
    <select name="class_id" class="dir-input" x-model="modal.classId">
      <option value="">— Aucune</option>
      @foreach($classes as $cls)
      <option value="{{ $cls->id }}">{{ $cls->name }}{{ $cls->filiere ? ' · '.$cls->filiere->code : '' }}</option>
      @endforeach
    </select>
  </div>
  @endif

  <div style="grid-column:1/-1;display:flex;align-items:center;gap:8px">
    <input type="hidden" name="is_active" value="0"/>
    <input type="checkbox" name="is_active" value="1" id="dir-active"
           :checked="modal.isActive" x-model="modal.isActive"
           style="width:15px;height:15px;accent-color:#7E5BEF;cursor:pointer"/>
    <label for="dir-active" style="font:500 12.5px/1 var(--f-ui);color:var(--ink-2);cursor:pointer">Compte actif</label>
  </div>
</div>
