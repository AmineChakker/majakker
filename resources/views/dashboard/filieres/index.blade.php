<x-layouts.app title="Filières" subtitle="Gestion scolaire">
@include('dashboard._mgmt_styles')

<div style="padding:28px 32px 64px;height:100%;overflow:auto" class="scroll" x-data="mgmtPage()">

  @include('dashboard._mgmt_alerts')

  {{-- Header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
      <span class="dir-eyebrow">Gestion des filières</span>
      <h2 style="font:400 32px/1.05 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        Filières <span style="font-size:18px;color:var(--ink-3)">({{ $filieres->count() }})</span>
      </h2>
    </div>
    <button @click="openCreate()" class="dir-btn-primary">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      Nouvelle filière
    </button>
  </div>

  {{-- Grid of filière cards --}}
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
    @forelse($filieres as $f)
    <div class="dir-card" style="border-left:3px solid var(--c-{{ $f->color }})">
      <div style="display:flex;align-items:flex-start;gap:12px">
        <div style="width:40px;height:40px;border-radius:10px;background:var(--c-{{ $f->color }}-soft);color:var(--c-{{ $f->color }});display:flex;align-items:center;justify-content:center;font:700 13px/1 var(--f-mono);flex-shrink:0">
          {{ $f->code ?: strtoupper(substr($f->name,0,2)) }}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font:600 14px/1.2 var(--f-ui)">{{ $f->name }}</div>
          @if($f->level)
          <div style="font:400 11px/1 var(--f-mono);color:var(--ink-3);margin-top:3px">{{ $f->level }}</div>
          @endif
          @if($f->description)
          <div style="font:400 12px/1.4 var(--f-ui);color:var(--ink-2);margin-top:6px">{{ Str::limit($f->description,80) }}</div>
          @endif
        </div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;padding-top:12px;border-top:0.5px solid var(--line)">
        <span style="font:500 11px/1 var(--f-mono);color:var(--ink-3)">{{ $f->classes_count }} classe(s)</span>
        <div style="display:flex;gap:6px">
          <button @click="openEdit({{ $f->id }},'{{ addslashes($f->name) }}','{{ addslashes($f->code ?? '') }}','{{ addslashes($f->level ?? '') }}','{{ addslashes($f->description ?? '') }}','{{ $f->color }}')"
                  class="dir-btn-ghost" style="height:28px;padding:0 10px;font-size:11px">Modifier</button>
          <form action="{{ route('dashboard.filieres.destroy',$f) }}" method="POST"
                onsubmit="return confirm('Supprimer « {{ addslashes($f->name) }} » ?')">
            @csrf @method('DELETE')
            <button type="submit" class="dir-btn-danger" style="height:28px;padding:0 10px;font-size:11px">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
    @empty
    <div style="grid-column:1/-1;padding:60px;text-align:center;color:var(--ink-3)">
      <x-ui.zellige-star size="40" color="var(--ink-4)" opacity="0.4"/>
      <div style="margin-top:12px;font:400 14px/1.5 var(--f-ui)">Aucune filière — créez-en une pour organiser vos classes.</div>
    </div>
    @endforelse
  </div>

  {{-- Modal --}}
  @include('dashboard._mgmt_modal', [
    'title_create' => 'Nouvelle filière',
    'title_edit'   => 'Modifier la filière',
    'store_route'  => route('dashboard.filieres.store'),
    'fields'       => 'dashboard.filieres._fields',
  ])
</div>

<script>
function mgmtPage() {
  return {
    modal: { open:false, editing:false, id:null, name:'', code:'', level:'', description:'', color:'blue' },
    openCreate() {
      this.modal = { open:true, editing:false, id:null, name:'', code:'', level:'', description:'', color:'blue' };
    },
    openEdit(id,name,code,level,description,color) {
      this.modal = { open:true, editing:true, id, name, code, level, description, color };
    },
  }
}
</script>
</x-layouts.app>
