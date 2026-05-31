<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
  <div style="grid-column:1/-1">
    <label class="dir-label">Nom de la classe *</label>
    <input name="name" class="dir-input" required placeholder="ex. Tle BAC · Sciences Math A" x-model="modal.name"/>
  </div>
  <div>
    <label class="dir-label">Filière</label>
    <select name="filiere_id" class="dir-input" x-model="modal.filiere_id">
      <option value="">— Sélectionner</option>
      @foreach($filieres as $f)
      <option value="{{ $f->id }}">{{ $f->name }}{{ $f->level ? ' · '.$f->level : '' }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="dir-label">Enseignant principal</label>
    <select name="teacher_id" class="dir-input" x-model="modal.teacher_id">
      <option value="">— Aucun</option>
      @foreach($teachers as $t)
      <option value="{{ $t->id }}">{{ $t->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="dir-label">Année scolaire</label>
    <input name="academic_year" class="dir-input" placeholder="2025-2026" x-model="modal.academic_year"/>
  </div>
  <div>
    <label class="dir-label">Capacité max</label>
    <input name="capacity" type="number" min="1" max="200" class="dir-input" x-model="modal.capacity"/>
  </div>
  <div style="grid-column:1/-1">
    <label class="dir-label">Description</label>
    <textarea name="description" class="dir-input" rows="2" placeholder="Optionnel…" x-model="modal.description" style="height:auto;padding-top:10px;padding-bottom:10px;resize:none"></textarea>
  </div>
  <div style="grid-column:1/-1">
    <label class="dir-label">Couleur</label>
    <div style="display:flex;gap:8px">
      @foreach(['blue'=>'Bleu','saffron'=>'Safran','atlas'=>'Vert','terracotta'=>'Terre'] as $val=>$lbl)
      <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;height:34px;border-radius:8px;border:0.5px solid var(--line-2);cursor:pointer;font:500 11.5px/1 var(--f-ui);color:var(--ink-2);transition:all .15s"
             :style="modal.color==='{{ $val }}' ? 'border-color:var(--c-{{ $val }});background:var(--c-{{ $val }}-soft);color:var(--c-{{ $val }})' : ''">
        <input type="radio" name="color" value="{{ $val }}" x-model="modal.color" style="display:none"/>
        <span style="width:8px;height:8px;border-radius:999px;background:var(--c-{{ $val }})"></span>{{ $lbl }}
      </label>
      @endforeach
    </div>
  </div>
</div>
