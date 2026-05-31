<x-layouts.app title="Classes" subtitle="Gestion scolaire">
@include('dashboard._mgmt_styles')

<div style="padding:28px 32px 64px;height:100%;overflow:auto" class="scroll"
     x-data="classesPage()">

  @include('dashboard._mgmt_alerts')

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
      <span class="dir-eyebrow">Gestion des classes</span>
      <h2 style="font:400 32px/1.05 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        Classes <span style="font-size:18px;color:var(--ink-3)">({{ $classes->count() }})</span>
      </h2>
    </div>
    <button @click="openCreate()" class="dir-btn-primary">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      Nouvelle classe
    </button>
  </div>

  <div style="background:var(--surface);border:0.5px solid var(--line);border-radius:14px;overflow:hidden">
    <table class="dir-table">
      <thead>
        <tr>
          @foreach(['Classe','Filière','Enseignant','Élèves','Capacité','Année','Couleur','Actions'] as $h)
          <th>{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($classes as $c)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:6px;height:32px;border-radius:3px;background:var(--c-{{ $c->color }});flex-shrink:0"></div>
              <div>
                <div style="font:600 13px/1.2 var(--f-ui);color:var(--ink)">{{ $c->name }}</div>
                @if($c->description)<div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ Str::limit($c->description,40) }}</div>@endif
              </div>
            </div>
          </td>
          <td>
            @if($c->filiere)
            <span style="padding:2px 8px;border-radius:999px;background:var(--c-{{ $c->filiere->color }}-soft);color:var(--c-{{ $c->filiere->color }});font:600 10px/1.8 var(--f-mono)">
              {{ $c->filiere->code ?: $c->filiere->name }}
            </span>
            @else<span style="color:var(--ink-4)">—</span>@endif
          </td>
          <td>{{ $c->teacher?->name ?? '—' }}</td>
          <td style="font:600 13px/1 var(--f-mono)">{{ $c->members_count }}</td>
          <td style="color:var(--ink-3);font:400 12px/1 var(--f-mono)">{{ $c->capacity }}</td>
          <td style="color:var(--ink-3);font:400 11.5px/1 var(--f-mono)">{{ $c->academic_year ?? '—' }}</td>
          <td>
            <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:var(--c-{{ $c->color }})"></span>
          </td>
          <td>
            <div class="row-actions" style="display:flex;gap:5px;opacity:0;transition:opacity .15s">
              <button @click="openEdit({{ $c->id }},'{{ addslashes($c->name) }}','{{ $c->filiere_id ?? '' }}','{{ $c->teacher_id ?? '' }}','{{ $c->color }}','{{ addslashes($c->academic_year ?? '') }}','{{ $c->capacity }}','{{ addslashes($c->description ?? '') }}')"
                      class="dir-btn-ghost" style="height:28px;padding:0 10px;font-size:11px">Modifier</button>
              <form action="{{ route('dashboard.classes.destroy',$c) }}" method="POST"
                    onsubmit="return confirm('Supprimer la classe « {{ addslashes($c->name) }} » ?')">
                @csrf @method('DELETE')
                <button type="submit" class="dir-btn-danger" style="height:28px;padding:0 10px;font-size:11px">Supprimer</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" style="padding:60px;text-align:center;color:var(--ink-3)">Aucune classe — créez-en une.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- CRUD Modal --}}
  <div x-show="modal.open" x-cloak
       style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:20px"
       @click.self="modal.open=false">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(4px)" @click="modal.open=false"></div>
    <div style="position:relative;width:100%;max-width:560px;background:var(--surface);border-radius:16px;border:0.5px solid var(--line);box-shadow:0 32px 80px -20px rgba(20,21,43,.22);overflow:hidden">
      <div style="display:flex;align-items:center;gap:12px;padding:20px 22px;border-bottom:0.5px solid var(--line)">
        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="classes" size="15" style="color:#fff"/>
        </div>
        <h3 style="font:400 20px/1 var(--f-display);margin:0" x-text="modal.editing ? 'Modifier la classe' : 'Nouvelle classe'"></h3>
        <button @click="modal.open=false" style="margin-left:auto;width:26px;height:26px;border-radius:7px;border:0.5px solid var(--line-2);background:var(--surface-2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ink-3)">
          <svg width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 4l12 12M16 4L4 16"/></svg>
        </button>
      </div>
      <div style="padding:20px 22px 22px;max-height:80vh;overflow:auto">
        <template x-if="!modal.editing">
          <form action="{{ route('dashboard.classes.store') }}" method="POST" style="display:flex;flex-direction:column;gap:12px">
            @csrf
            @include('dashboard.classes._fields')
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" class="dir-btn-primary" style="flex:1;justify-content:center">Créer la classe</button>
              <button type="button" @click="modal.open=false" class="dir-btn-ghost">Annuler</button>
            </div>
          </form>
        </template>
        <template x-if="modal.editing">
          <form :action="'/dashboard/classes/'+modal.id" method="POST" style="display:flex;flex-direction:column;gap:12px">
            @csrf <input type="hidden" name="_method" value="PATCH"/>
            @include('dashboard.classes._fields')
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" class="dir-btn-primary" style="flex:1;justify-content:center">Enregistrer</button>
              <button type="button" @click="modal.open=false" class="dir-btn-ghost">Annuler</button>
            </div>
          </form>
        </template>
      </div>
    </div>
  </div>
</div>

<script>
function classesPage() {
  return {
    modal:{open:false,editing:false,id:null,name:'',filiere_id:'',teacher_id:'',color:'blue',academic_year:'',capacity:35,description:''},
    openCreate(){this.modal={open:true,editing:false,id:null,name:'',filiere_id:'',teacher_id:'',color:'blue',academic_year:'{{ now()->year.'-'.(now()->year+1) }}',capacity:35,description:''}},
    openEdit(id,name,filiere_id,teacher_id,color,academic_year,capacity,description){
      this.modal={open:true,editing:true,id,name,filiere_id:String(filiere_id||''),teacher_id:String(teacher_id||''),color,academic_year,capacity:Number(capacity)||35,description};
    },
  }
}
</script>
</x-layouts.app>
