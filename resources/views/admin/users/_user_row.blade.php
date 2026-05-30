@php
  $roleColors = ['admin'=>'role-admin','director'=>'role-director','teacher'=>'role-teacher','student'=>'role-student'];
  $roleLabels = ['admin'=>'Admin','director'=>'Directeur','teacher'=>'Enseignant','student'=>'Élève'];
  $pad = $indent ? '56px' : '18px';
@endphp
<div class="user-row" style="padding-left:{{ $pad }};border-top:0.5px solid var(--mj-line)"
     x-data>
  <x-ui.avatar :name="$u->name" size="30"/>
  <div style="flex:1;min-width:0">
    <div style="font:500 13px/1.2 var(--f-ui)">{{ $u->name }}</div>
    <div style="font:400 11px/1 var(--f-ui);color:var(--mj-ink-3);margin-top:2px">{{ $u->email }}</div>
  </div>
  <span class="adm-badge {{ $roleColors[$u->role] ?? '' }}" style="font-size:8.5px">
    {{ $roleLabels[$u->role] ?? $u->role }}
  </span>
  <span style="display:inline-flex;align-items:center;gap:4px;height:18px;padding:0 8px;border-radius:999px;font:600 9px/1 var(--f-mono);margin-left:6px;
    {{ $u->is_active ? 'background:rgba(16,185,129,.10);color:#047857' : 'background:rgba(220,38,38,.08);color:#B91C1C' }}">
    <span style="width:4px;height:4px;border-radius:999px;background:currentColor"></span>
    {{ $u->is_active ? 'Actif' : 'Inactif' }}
  </span>
  <div class="user-actions" style="margin-left:10px">
    <button @click="$dispatch('open-edit',{id:{{ $u->id }},name:'{{ addslashes($u->name) }}',email:'{{ addslashes($u->email) }}',role:'{{ $u->role }}',schoolId:{{ $u->school_id ?? 'null' }},isActive:{{ $u->is_active ? 'true' : 'false' }}})"
            style="height:26px;padding:0 10px;border-radius:7px;border:0.5px solid var(--mj-line-2);background:var(--mj-surface);font:500 10.5px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">
      Modifier
    </button>
    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display:inline"
          onsubmit="return confirm('Supprimer {{ addslashes($u->name) }} ?')">
      @csrf @method('DELETE')
      <button type="submit" style="height:26px;padding:0 10px;border-radius:7px;border:0.5px solid rgba(220,38,38,.2);background:rgba(220,38,38,.05);font:500 10.5px/1 var(--f-ui);color:#B91C1C;cursor:pointer">
        Supprimer
      </button>
    </form>
  </div>
</div>
