<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
  <div style="grid-column:1/-1">
    <label class="dir-label">Nom de la filière *</label>
    <input name="name" class="dir-input" required placeholder="Sciences Mathématiques" x-model="modal.name"/>
  </div>
  <div>
    <label class="dir-label">Code</label>
    <input name="code" class="dir-input" placeholder="SM" x-model="modal.code"/>
  </div>
  <div>
    <label class="dir-label">Niveau</label>
    <select name="level" class="dir-input" x-model="modal.level">
      <option value="">— Sélectionner</option>
      @foreach(['Tronc commun','1ère Bac','2ème Bac','3ème Bac','BTS'] as $l)
      <option value="{{ $l }}">{{ $l }}</option>
      @endforeach
    </select>
  </div>
  <div style="grid-column:1/-1">
    <label class="dir-label">Description</label>
    <textarea name="description" class="dir-input" rows="2" placeholder="Description optionnelle…" x-model="modal.description" style="height:auto;padding-top:10px;padding-bottom:10px;resize:none"></textarea>
  </div>
  <div style="grid-column:1/-1">
    <label class="dir-label">Couleur</label>
    <div style="display:flex;gap:8px">
      @foreach(['blue'=>'Bleu','saffron'=>'Safran','atlas'=>'Vert','terracotta'=>'Terre'] as $val=>$lbl)
      <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;height:36px;border-radius:9px;border:0.5px solid var(--line-2);cursor:pointer;font:500 12px/1 var(--f-ui);color:var(--ink-2);transition:all .15s"
             :style="modal.color==='{{ $val }}' ? 'border-color:var(--c-{{ $val }});background:var(--c-{{ $val }}-soft);color:var(--c-{{ $val }})' : ''">
        <input type="radio" name="color" value="{{ $val }}" x-model="modal.color" style="display:none"/>
        <span style="width:8px;height:8px;border-radius:999px;background:var(--c-{{ $val }})"></span>
        {{ $lbl }}
      </label>
      @endforeach
    </div>
  </div>
</div>
