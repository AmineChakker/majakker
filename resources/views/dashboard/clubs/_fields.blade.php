<div style="display:flex;flex-direction:column;gap:12px">
  <div>
    <label class="dir-label">Nom du club *</label>
    <input name="name" class="dir-input" required placeholder="ex. Club Astronomie, Club Photo…" x-model="modal.name"/>
  </div>
  <div>
    <label class="dir-label">Superviseur (enseignant)</label>
    <select name="teacher_id" class="dir-input" x-model="modal.teacher_id">
      <option value="">— Aucun</option>
      @foreach($teachers as $t)
      <option value="{{ $t->id }}">{{ $t->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="dir-label">Description</label>
    <textarea name="description" class="dir-input" rows="3" placeholder="Décrivez les activités du club…" x-model="modal.description" style="height:auto;padding-top:10px;padding-bottom:10px;resize:none"></textarea>
  </div>
  <div>
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
