<x-layouts.app title="Enseignants" :subtitle="$teachers->total().' membres'">
@include('dashboard._mgmt_styles')

<div style="padding:28px 32px 64px;height:100%;overflow:auto" class="scroll"
     x-data="teachersPage()">

  @include('dashboard._mgmt_alerts')

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
      <span class="dir-eyebrow">Gestion des enseignants</span>
      <h2 style="font:400 32px/1.05 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        Enseignants <span style="font-size:18px;color:var(--ink-3)">({{ $teachers->total() }})</span>
      </h2>
    </div>
    <button @click="openCreate()" class="dir-btn-primary">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      Ajouter un enseignant
    </button>
  </div>

  <div style="background:var(--surface);border:0.5px solid var(--line);border-radius:14px;overflow:hidden">
    <table class="dir-table">
      <thead>
        <tr>@foreach(['Enseignant','Email','Classes assignées','Statut','Inscrit le','Actions'] as $h)<th>{{ $h }}</th>@endforeach</tr>
      </thead>
      <tbody>
        @forelse($teachers as $t)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <x-ui.avatar :name="$t->name" size="32"/>
              <span style="font:600 13px/1.2 var(--f-ui);color:var(--ink)">{{ $t->name }}</span>
            </div>
          </td>
          <td style="color:var(--ink-2)">{{ $t->email }}</td>
          <td>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @forelse($t->groups->where('kind','class')->take(3) as $g)
              <span style="padding:2px 7px;border-radius:999px;background:var(--c-{{ $g->color }}-soft);color:var(--c-{{ $g->color }});font:500 10px/1.6 var(--f-mono)">{{ $g->name }}</span>
              @empty<span style="color:var(--ink-4);font-size:12px">—</span>@endforelse
              @if($t->groups->count() > 3)<span style="font:400 10.5px/1 var(--f-mono);color:var(--ink-3)">+{{ $t->groups->count()-3 }}</span>@endif
            </div>
          </td>
          <td>
            <span style="display:inline-flex;align-items:center;gap:4px;height:18px;padding:0 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);
              {{ $t->is_active ? 'background:rgba(16,185,129,.10);color:#047857' : 'background:rgba(220,38,38,.08);color:#B91C1C' }}">
              <span style="width:4px;height:4px;border-radius:999px;background:currentColor"></span>
              {{ $t->is_active ? 'Actif' : 'Inactif' }}
            </span>
          </td>
          <td style="font:400 11.5px/1 var(--f-mono);color:var(--ink-3)">{{ $t->created_at->format('d M Y') }}</td>
          <td>
            <div class="row-actions" style="display:flex;gap:5px;opacity:0;transition:opacity .15s">
              <button @click="openEdit({{ $t->id }},'{{ addslashes($t->name) }}','{{ addslashes($t->email) }}',{{ $t->is_active ? 'true' : 'false' }},{{ $t->groups->where('kind','class')->pluck('id') }})"
                      class="dir-btn-ghost" style="height:28px;padding:0 10px;font-size:11px">Modifier</button>
              <form action="{{ route('dashboard.teachers.destroy',$t) }}" method="POST"
                    onsubmit="return confirm('Supprimer « {{ addslashes($t->name) }} » ?')">
                @csrf @method('DELETE')
                <button type="submit" class="dir-btn-danger" style="height:28px;padding:0 10px;font-size:11px">Supprimer</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="padding:60px;text-align:center;color:var(--ink-3)">Aucun enseignant enregistré.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px">{{ $teachers->links() }}</div>

  {{-- CRUD Modal --}}
  <div x-show="modal.open" x-cloak
       style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:20px"
       @click.self="modal.open=false">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(4px)" @click="modal.open=false"></div>
    <div style="position:relative;width:100%;max-width:540px;background:var(--surface);border-radius:16px;border:0.5px solid var(--line);box-shadow:0 32px 80px -20px rgba(20,21,43,.22);overflow:hidden">
      <div style="display:flex;align-items:center;gap:12px;padding:20px 22px;border-bottom:0.5px solid var(--line)">
        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="users" size="15" style="color:#fff"/>
        </div>
        <h3 style="font:400 20px/1 var(--f-display);margin:0" x-text="modal.editing ? 'Modifier l\'enseignant' : 'Nouvel enseignant'"></h3>
        <button @click="modal.open=false" style="margin-left:auto;width:26px;height:26px;border-radius:7px;border:0.5px solid var(--line-2);background:var(--surface-2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ink-3)">
          <svg width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 4l12 12M16 4L4 16"/></svg>
        </button>
      </div>
      <div style="padding:20px 22px 22px;max-height:80vh;overflow:auto">
        <template x-if="!modal.editing">
          <form action="{{ route('dashboard.teachers.store') }}" method="POST" style="display:flex;flex-direction:column;gap:12px">
            @csrf
            @include('dashboard._user_fields', ['showClasses' => true])
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" class="dir-btn-primary" style="flex:1;justify-content:center">Créer l'enseignant</button>
              <button type="button" @click="modal.open=false" class="dir-btn-ghost">Annuler</button>
            </div>
          </form>
        </template>
        <template x-if="modal.editing">
          <form :action="'/dashboard/teachers/'+modal.id" method="POST" style="display:flex;flex-direction:column;gap:12px">
            @csrf <input type="hidden" name="_method" value="PATCH"/>
            @include('dashboard._user_fields', ['showClasses' => true])
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
function teachersPage() {
  return {
    modal:{open:false,editing:false,id:null,name:'',email:'',password:'',isActive:true,classIds:[]},
    openCreate(){this.modal={open:true,editing:false,id:null,name:'',email:'',password:'',isActive:true,classIds:[]}},
    openEdit(id,name,email,isActive,classIds){this.modal={open:true,editing:true,id,name,email,password:'',isActive,classIds:classIds||[]}},
  }
}
</script>
</x-layouts.app>
