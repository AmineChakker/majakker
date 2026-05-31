<x-layouts.app title="Élèves" :subtitle="$students->total().' inscrits'">
@include('dashboard._mgmt_styles')

<div style="padding:28px 32px 64px;height:100%;overflow:auto" class="scroll"
     x-data="studentsPage()">

  @include('dashboard._mgmt_alerts')

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <div>
      <span class="dir-eyebrow">Gestion des élèves</span>
      <h2 style="font:400 32px/1.05 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        Élèves <span style="font-size:18px;color:var(--ink-3)">({{ $students->total() }})</span>
      </h2>
    </div>
    <button @click="openCreate()" class="dir-btn-primary">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      Inscrire un élève
    </button>
  </div>

  {{-- Filter by class --}}
  <div style="display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap">
    <a href="{{ route('dashboard.students') }}"
       style="height:30px;padding:0 12px;border-radius:8px;font:500 11.5px/30px var(--f-ui);text-decoration:none;transition:all .15s;
              {{ !$classFilter ? 'background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff' : 'background:var(--surface);border:0.5px solid var(--line-2);color:var(--ink-2)' }}">
      Tous
    </a>
    @foreach($classes as $cls)
    <a href="{{ route('dashboard.students',['class_id'=>$cls->id]) }}"
       style="height:30px;padding:0 12px;border-radius:8px;font:500 11.5px/30px var(--f-ui);text-decoration:none;transition:all .15s;border-left:2px solid var(--c-{{ $cls->color }});
              {{ $classFilter == $cls->id ? 'background:var(--c-'.$cls->color.'-soft);color:var(--c-'.$cls->color.')' : 'background:var(--surface);border:0.5px solid var(--line-2);border-left:2px solid var(--c-'.$cls->color.');color:var(--ink-2)' }}">
      {{ $cls->name }}
    </a>
    @endforeach
  </div>

  <div style="background:var(--surface);border:0.5px solid var(--line);border-radius:14px;overflow:hidden">
    <table class="dir-table">
      <thead>
        <tr>@foreach(['Élève','Email','Classe','Publications','Statut','Inscrit le','Actions'] as $h)<th>{{ $h }}</th>@endforeach</tr>
      </thead>
      <tbody>
        @forelse($students as $s)
        @php $class = $s->groups->where('kind','class')->first(); @endphp
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <x-ui.avatar :name="$s->name" size="32"/>
              <div>
                <div style="font:600 13px/1.2 var(--f-ui);color:var(--ink)">{{ $s->name }}</div>
                <div style="font:400 10.5px/1 var(--f-mono);color:var(--ink-4)">@{{ $s->handle }}</div>
              </div>
            </div>
          </td>
          <td style="color:var(--ink-2)">{{ $s->email }}</td>
          <td>
            @if($class)
            <span style="padding:2px 8px;border-radius:999px;background:var(--c-{{ $class->color }}-soft);color:var(--c-{{ $class->color }});font:500 10px/1.7 var(--f-mono)">
              {{ $class->name }}
            </span>
            @else<span style="color:var(--ink-4)">—</span>@endif
          </td>
          <td style="font:600 12px/1 var(--f-mono)">{{ $s->posts()->count() }}</td>
          <td>
            <span style="display:inline-flex;align-items:center;gap:4px;height:18px;padding:0 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);
              {{ $s->is_active ? 'background:rgba(16,185,129,.10);color:#047857' : 'background:rgba(220,38,38,.08);color:#B91C1C' }}">
              <span style="width:4px;height:4px;border-radius:999px;background:currentColor"></span>
              {{ $s->is_active ? 'Actif' : 'Inactif' }}
            </span>
          </td>
          <td style="font:400 11.5px/1 var(--f-mono);color:var(--ink-3)">{{ $s->created_at->format('d M Y') }}</td>
          <td>
            <div class="row-actions" style="display:flex;gap:5px;opacity:0;transition:opacity .15s">
              <button @click="openEdit({{ $s->id }},'{{ addslashes($s->name) }}','{{ addslashes($s->email) }}',{{ $s->is_active?'true':'false' }},{{ $class?->id ?? 'null' }})"
                      class="dir-btn-ghost" style="height:28px;padding:0 10px;font-size:11px">Modifier</button>
              <form action="{{ route('dashboard.students.destroy',$s) }}" method="POST"
                    onsubmit="return confirm('Supprimer « {{ addslashes($s->name) }} » ?')">
                @csrf @method('DELETE')
                <button type="submit" class="dir-btn-danger" style="height:28px;padding:0 10px;font-size:11px">Supprimer</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="padding:60px;text-align:center;color:var(--ink-3)">Aucun élève inscrit.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px">{{ $students->links() }}</div>

  {{-- CRUD Modal --}}
  <div x-show="modal.open" x-cloak
       style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:20px"
       @click.self="modal.open=false">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(4px)" @click="modal.open=false"></div>
    <div style="position:relative;width:100%;max-width:500px;background:var(--surface);border-radius:16px;border:0.5px solid var(--line);box-shadow:0 32px 80px -20px rgba(20,21,43,.22);overflow:hidden">
      <div style="display:flex;align-items:center;gap:12px;padding:20px 22px;border-bottom:0.5px solid var(--line)">
        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="users" size="15" style="color:#fff"/>
        </div>
        <h3 style="font:400 20px/1 var(--f-display);margin:0" x-text="modal.editing ? 'Modifier l\'élève' : 'Inscrire un élève'"></h3>
        <button @click="modal.open=false" style="margin-left:auto;width:26px;height:26px;border-radius:7px;border:0.5px solid var(--line-2);background:var(--surface-2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ink-3)">
          <svg width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 4l12 12M16 4L4 16"/></svg>
        </button>
      </div>
      <div style="padding:20px 22px 22px">
        <template x-if="!modal.editing">
          <form action="{{ route('dashboard.students.store') }}" method="POST" style="display:flex;flex-direction:column;gap:12px">
            @csrf
            @include('dashboard._user_fields', ['showClasses' => false, 'singleClass' => true])
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" class="dir-btn-primary" style="flex:1;justify-content:center">Inscrire l'élève</button>
              <button type="button" @click="modal.open=false" class="dir-btn-ghost">Annuler</button>
            </div>
          </form>
        </template>
        <template x-if="modal.editing">
          <form :action="'/dashboard/students/'+modal.id" method="POST" style="display:flex;flex-direction:column;gap:12px">
            @csrf <input type="hidden" name="_method" value="PATCH"/>
            @include('dashboard._user_fields', ['showClasses' => false, 'singleClass' => true])
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
function studentsPage() {
  return {
    modal:{open:false,editing:false,id:null,name:'',email:'',password:'',isActive:true,classId:''},
    openCreate(){this.modal={open:true,editing:false,id:null,name:'',email:'',password:'',isActive:true,classId:'{{ $classFilter ?? '' }}'}},
    openEdit(id,name,email,isActive,classId){this.modal={open:true,editing:true,id,name,email,password:'',isActive,classId:classId?String(classId):''}},
  }
}
</script>
</x-layouts.app>
