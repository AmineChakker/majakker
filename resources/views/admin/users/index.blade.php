<x-layouts.admin title="Utilisateurs" subtitle="Plateforme">
<style>
/* ── role badge colors ── */
.role-admin    {background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff}
.role-director {background:rgba(245,158,11,.13);color:#B45309}
.role-teacher  {background:rgba(6,182,212,.11);color:#0E7490}
.role-student  {background:rgba(16,185,129,.11);color:#047857}

/* ── tree node ── */
.tree-school{background:var(--mj-surface);border:0.5px solid var(--mj-line);border-radius:14px;overflow:hidden;transition:border-color .2s}
.tree-school:hover{border-color:rgba(126,91,239,.25)}
.tree-header{display:flex;align-items:center;gap:12px;padding:14px 18px;cursor:pointer;user-select:none;transition:background .15s}
.tree-header:hover{background:var(--surface-2)}
.tree-section{border-top:0.5px solid var(--mj-line)}
.tree-section-h{display:flex;align-items:center;gap:10px;padding:10px 18px 10px 42px;cursor:pointer;user-select:none;background:var(--surface-2)}
.tree-section-h:hover{background:var(--surface-3)}
.user-row{display:flex;align-items:center;gap:12px;padding:10px 18px 10px 56px;border-top:0.5px solid var(--mj-line);transition:background .15s}
.user-row:hover{background:rgba(126,91,239,.03)}
.user-row:hover .user-actions{opacity:1}
.user-actions{opacity:0;display:flex;gap:5px;transition:opacity .15s}
.adm-badge{display:inline-flex;align-items:center;height:18px;padding:0 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);letter-spacing:.08em;text-transform:uppercase}
.adm-input{width:100%;height:40px;padding:0 12px;border-radius:9px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--mj-ink);outline:none;box-sizing:border-box}
.adm-input:focus{border-color:#7E5BEF;box-shadow:0 0 0 3px rgba(126,91,239,.12)}
.adm-label{font:500 11.5px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:5px}
</style>

<div style="padding:28px 32px 64px;height:100%;overflow:auto" class="adm-scroll"
     x-data="usersPage()" x-init="init()"
     @open-edit.window="openEdit($event.detail.id,$event.detail.name,$event.detail.email,$event.detail.role,$event.detail.schoolId,$event.detail.isActive)">

  {{-- ── Alerts ── --}}
  @if(session('success'))
  <div style="margin-bottom:20px;padding:13px 16px;border-radius:10px;background:rgba(16,185,129,.08);border:0.5px solid rgba(16,185,129,.25);color:#047857;font:500 13px/1 var(--f-ui)">
    {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div style="margin-bottom:20px;padding:13px 16px;border-radius:10px;background:rgba(220,38,38,.07);border:0.5px solid rgba(220,38,38,.2);color:#B91C1C;font:500 13px/1 var(--f-ui)">
    {{ session('error') }}
  </div>
  @endif

  {{-- ── Header ── --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
      <span class="adm-eyebrow">Gestion des utilisateurs</span>
      <h2 style="font:400 32px/1.05 var(--f-display);margin:8px 0 0;letter-spacing:-.02em">
        {{ array_sum($counts->toArray()) }} utilisateurs
      </h2>
    </div>
    <button @click="openCreate()" class="adm-cta">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
      Nouvel utilisateur
    </button>
  </div>

  {{-- ── Role tabs ── --}}
  <div style="display:flex;gap:4px;margin-bottom:22px;flex-wrap:wrap">
    @php
      $tabs = [
        'tree'     => ['label' => 'Arborescence', 'icon' => 'grid', 'count' => null],
        'admin'    => ['label' => 'Admins',        'icon' => null, 'count' => $counts['admin'] ?? 0],
        'director' => ['label' => 'Directeurs',    'icon' => null, 'count' => $counts['director'] ?? 0],
        'teacher'  => ['label' => 'Enseignants',   'icon' => null, 'count' => $counts['teacher'] ?? 0],
        'student'  => ['label' => 'Élèves',        'icon' => null, 'count' => $counts['student'] ?? 0],
      ];
    @endphp
    @foreach($tabs as $key => $tab)
    @php $isActive = $role === $key; @endphp
    <a href="{{ route('admin.users', ['role' => $key]) }}"
       style="display:inline-flex;align-items:center;gap:7px;height:34px;padding:0 14px;border-radius:10px;font:500 12.5px/1 var(--f-ui);text-decoration:none;transition:all .15s;
              {{ $isActive ? 'background:var(--mj-gradient);color:#fff;box-shadow:0 4px 14px -4px rgba(94,57,224,.4)' : 'background:var(--mj-surface);color:var(--mj-ink-2);border:0.5px solid var(--mj-line-2)' }}">
      @if($tab['icon'])
      <x-ui.icon name="{{ $tab['icon'] }}" size="13"/>
      @endif
      {{ $tab['label'] }}
      @if($tab['count'] !== null)
      <span style="min-width:20px;height:18px;padding:0 6px;border-radius:999px;font:600 10px/18px var(--f-mono);text-align:center;
                   {{ $isActive ? 'background:rgba(255,255,255,.22);color:#fff' : 'background:var(--surface-2);color:var(--mj-ink-3)' }}">
        {{ $tab['count'] }}
      </span>
      @endif
    </a>
    @endforeach
  </div>

  {{-- ════════════════════════════════════════════════════════ --}}
  {{-- TREE VIEW --}}
  {{-- ════════════════════════════════════════════════════════ --}}
  @if($role === 'tree')

    {{-- Platform admins --}}
    @if($admins->count())
    <div class="tree-school" style="margin-bottom:12px">
      <div class="tree-header" style="cursor:default">
        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="moderation" size="16" style="color:#fff"/>
        </div>
        <div style="flex:1">
          <div style="font:600 13.5px/1.2 var(--f-ui)">Administrateurs plateforme</div>
          <div style="font:400 11px/1 var(--f-mono);color:var(--mj-ink-3);margin-top:3px">EduSphere SA · {{ $admins->count() }} compte(s)</div>
        </div>
        <span class="adm-badge role-admin">ADMIN</span>
      </div>
      @foreach($admins as $u)
      @include('admin.users._user_row', ['u' => $u, 'indent' => false])
      @endforeach
    </div>
    @endif

    {{-- Schools tree --}}
    @foreach($schools as $school)
    @php
      $director = $school->users->firstWhere('role','director');
      $teachers = $school->users->where('role','teacher');
      $students = $school->users->where('role','student');
    @endphp
    <div class="tree-school" style="margin-bottom:12px" x-data="{ open: true }">

      {{-- School header --}}
      <div class="tree-header" @click="open = !open">
        <div style="width:34px;height:34px;border-radius:9px;background:var(--mj-gradient-soft);color:var(--mj-purple);display:flex;align-items:center;justify-content:center;font:600 14px/1 var(--f-ui);flex-shrink:0">
          {{ $school->initial }}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font:600 13.5px/1.2 var(--f-ui)">{{ $school->name }}</div>
          <div style="font:400 11px/1 var(--f-mono);color:var(--mj-ink-3);margin-top:3px">
            {{ $school->city }} · {{ $school->users->count() }} membres
          </div>
        </div>
        @php $t = match($school->plan) { 'pro'=>'pro','school'=>'school',default=>'starter' }; @endphp
        <span class="adm-tier {{ $t }}" style="margin-right:8px">{{ strtoupper($school->plan ?? 'STARTER') }}</span>
        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="var(--mj-ink-3)" stroke-width="1.5" stroke-linecap="round"
             :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s;flex-shrink:0">
          <path d="M5 8l5 5 5-5"/>
        </svg>
      </div>

      {{-- School body --}}
      <div x-show="open" x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        {{-- Director --}}
        @if($director)
        <div class="tree-section">
          <div class="tree-section-h" style="cursor:default">
            <span style="width:5px;height:5px;border-radius:999px;background:#B45309;flex-shrink:0"></span>
            <span style="font:500 10.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:#B45309">Directeur</span>
          </div>
          @include('admin.users._user_row', ['u' => $director, 'indent' => true])
        </div>
        @else
        <div class="tree-section" style="padding:10px 18px 10px 42px">
          <span style="font:400 11.5px/1 var(--f-ui);color:var(--mj-ink-3)">Aucun directeur —
            <button @click="openCreate('director',{{ $school->id }})" style="border:0;background:0;cursor:pointer;font:500 11.5px/1 var(--f-ui);color:var(--mj-purple);padding:0">ajouter</button>
          </span>
        </div>
        @endif

        {{-- Teachers --}}
        @if($teachers->count())
        <div class="tree-section" x-data="{ open: false }">
          <div class="tree-section-h" @click="open = !open">
            <span style="width:5px;height:5px;border-radius:999px;background:#0E7490;flex-shrink:0"></span>
            <span style="font:500 10.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:#0E7490">Enseignants</span>
            <span style="margin-left:8px;min-width:20px;height:16px;padding:0 5px;border-radius:999px;background:rgba(6,182,212,.11);color:#0E7490;font:600 9.5px/16px var(--f-mono);text-align:center">{{ $teachers->count() }}</span>
            <span style="flex:1"></span>
            <button @click.stop="openCreate('teacher',{{ $school->id }})" style="border:0;background:0;cursor:pointer;font:500 10.5px/1 var(--f-ui);color:var(--mj-ink-3);padding:2px 6px;border-radius:5px" title="Ajouter un enseignant">
              <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
            </button>
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--mj-ink-3)" stroke-width="1.5" stroke-linecap="round"
                 :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s;margin-left:4px">
              <path d="M5 8l5 5 5-5"/>
            </svg>
          </div>
          <div x-show="open" x-transition>
            @foreach($teachers as $u)
            @include('admin.users._user_row', ['u' => $u, 'indent' => true])
            @endforeach
          </div>
        </div>
        @else
        <div class="tree-section" style="padding:10px 18px 10px 42px;display:flex;align-items:center;gap:8px">
          <span style="font:400 11.5px/1 var(--f-ui);color:var(--mj-ink-3)">Aucun enseignant —</span>
          <button @click="openCreate('teacher',{{ $school->id }})" style="border:0;background:0;cursor:pointer;font:500 11.5px/1 var(--f-ui);color:var(--mj-purple);padding:0">ajouter</button>
        </div>
        @endif

        {{-- Students --}}
        @if($students->count())
        <div class="tree-section" x-data="{ open: false }">
          <div class="tree-section-h" @click="open = !open">
            <span style="width:5px;height:5px;border-radius:999px;background:#047857;flex-shrink:0"></span>
            <span style="font:500 10.5px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:#047857">Élèves</span>
            <span style="margin-left:8px;min-width:20px;height:16px;padding:0 5px;border-radius:999px;background:rgba(16,185,129,.11);color:#047857;font:600 9.5px/16px var(--f-mono);text-align:center">{{ $students->count() }}</span>
            <span style="flex:1"></span>
            <button @click.stop="openCreate('student',{{ $school->id }})" style="border:0;background:0;cursor:pointer;font:500 10.5px/1 var(--f-ui);color:var(--mj-ink-3);padding:2px 6px;border-radius:5px" title="Ajouter un élève">
              <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
            </button>
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="var(--mj-ink-3)" stroke-width="1.5" stroke-linecap="round"
                 :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s;margin-left:4px">
              <path d="M5 8l5 5 5-5"/>
            </svg>
          </div>
          <div x-show="open" x-transition>
            @foreach($students as $u)
            @include('admin.users._user_row', ['u' => $u, 'indent' => true])
            @endforeach
          </div>
        </div>
        @else
        <div class="tree-section" style="padding:10px 18px 10px 42px;display:flex;align-items:center;gap:8px">
          <span style="font:400 11.5px/1 var(--f-ui);color:var(--mj-ink-3)">Aucun élève —</span>
          <button @click="openCreate('student',{{ $school->id }})" style="border:0;background:0;cursor:pointer;font:500 11.5px/1 var(--f-ui);color:var(--mj-purple);padding:0">ajouter</button>
        </div>
        @endif

      </div>{{-- /school body --}}
    </div>
    @endforeach

    {{-- Orphan users (no school) --}}
    @if($orphans->count())
    <div class="tree-school" x-data="{ open: false }">
      <div class="tree-header" @click="open = !open">
        <div style="width:34px;height:34px;border-radius:9px;background:rgba(20,21,43,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="users" size="16" style="color:var(--mj-ink-3)"/>
        </div>
        <div style="flex:1">
          <div style="font:600 13.5px/1.2 var(--f-ui)">Sans école assignée</div>
          <div style="font:400 11px/1 var(--f-mono);color:var(--mj-ink-3);margin-top:3px">{{ $orphans->count() }} utilisateur(s)</div>
        </div>
        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="var(--mj-ink-3)" stroke-width="1.5" stroke-linecap="round"
             :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s">
          <path d="M5 8l5 5 5-5"/>
        </svg>
      </div>
      <div x-show="open" x-transition>
        @foreach($orphans as $u)
        @include('admin.users._user_row', ['u' => $u, 'indent' => false])
        @endforeach
      </div>
    </div>
    @endif

  {{-- ════════════════════════════════════════════════════════ --}}
  {{-- FLAT ROLE TABLE --}}
  {{-- ════════════════════════════════════════════════════════ --}}
  @else
  @php
    $roleColors = ['admin'=>'role-admin','director'=>'role-director','teacher'=>'role-teacher','student'=>'role-student'];
    $roleLabels = ['admin'=>'Admin','director'=>'Directeur','teacher'=>'Enseignant','student'=>'Élève'];
  @endphp
  <div style="background:var(--mj-surface);border:0.5px solid var(--mj-line);border-radius:14px;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="background:var(--surface-2);border-bottom:0.5px solid var(--mj-line)">
          @foreach(['Utilisateur','Email','École','Statut','Inscrit le','Actions'] as $h)
          <th style="text-align:left;padding:12px 16px;font:500 10px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--mj-ink-3)">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
        <tr style="border-bottom:0.5px solid var(--mj-line);transition:background .15s"
            onmouseenter="this.querySelector('.row-actions').style.opacity='1'"
            onmouseleave="this.querySelector('.row-actions').style.opacity='0'">
          <td style="padding:12px 16px">
            <div style="display:flex;align-items:center;gap:10px">
              <x-ui.avatar :name="$u->name" size="32"/>
              <div>
                <div style="font:600 13px/1.2 var(--f-ui)">{{ $u->name }}</div>
                <span class="adm-badge {{ $roleColors[$u->role] ?? '' }}" style="margin-top:3px;font-size:8.5px">{{ $roleLabels[$u->role] ?? $u->role }}</span>
              </div>
            </div>
          </td>
          <td style="padding:12px 16px;font:400 12.5px/1 var(--f-ui);color:var(--mj-ink-2)">{{ $u->email }}</td>
          <td style="padding:12px 16px;font:400 12px/1 var(--f-ui);color:var(--mj-ink-2)">{{ $u->school?->name ?? '—' }}</td>
          <td style="padding:12px 16px">
            <span style="display:inline-flex;align-items:center;gap:4px;height:18px;padding:0 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);
              {{ $u->is_active ? 'background:rgba(16,185,129,.10);color:#047857' : 'background:rgba(220,38,38,.08);color:#B91C1C' }}">
              <span style="width:4px;height:4px;border-radius:999px;background:currentColor"></span>
              {{ $u->is_active ? 'Actif' : 'Inactif' }}
            </span>
          </td>
          <td style="padding:12px 16px;font:400 11.5px/1 var(--f-mono);color:var(--mj-ink-3)">{{ $u->created_at->format('d M Y') }}</td>
          <td style="padding:12px 16px">
            <div class="row-actions" style="display:flex;gap:5px;opacity:0;transition:opacity .15s">
              <button @click="openEdit({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}', '{{ $u->role }}', {{ $u->school_id ?? 'null' }}, {{ $u->is_active ? 'true' : 'false' }})"
                      style="height:28px;padding:0 10px;border-radius:7px;border:0.5px solid var(--mj-line-2);background:var(--mj-surface);font:500 11px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">
                Modifier
              </button>
              <form action="{{ route('admin.users.destroy', $u) }}" method="POST"
                    onsubmit="return confirm('Supprimer {{ addslashes($u->name) }} ?')">
                @csrf @method('DELETE')
                <button type="submit" style="height:28px;padding:0 10px;border-radius:7px;border:0.5px solid rgba(220,38,38,.2);background:rgba(220,38,38,.05);font:500 11px/1 var(--f-ui);color:#B91C1C;cursor:pointer">
                  Supprimer
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="padding:60px;text-align:center;color:var(--mj-ink-3)">Aucun utilisateur dans cette catégorie</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px">{{ $users->links() }}</div>
  @endif

  {{-- ══ CREATE / EDIT MODAL ══ --}}
  <div x-show="modal.open" x-cloak
       style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:20px"
       @click.self="modal.open=false">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(4px)" @click="modal.open=false"></div>
    <div style="position:relative;width:100%;max-width:520px;background:var(--mj-surface);border-radius:18px;border:0.5px solid var(--mj-line);box-shadow:0 32px 80px -20px rgba(20,21,43,.25);overflow:hidden">

      {{-- Modal header --}}
      <div style="display:flex;align-items:center;gap:12px;padding:22px 24px;border-bottom:0.5px solid var(--mj-line)">
        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="users" size="16" style="color:#fff"/>
        </div>
        <div>
          <h3 style="font:400 20px/1 var(--f-display);margin:0" x-text="modal.editing ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur'"></h3>
          <p style="font:400 11.5px/1 var(--f-ui);color:var(--mj-ink-3);margin:4px 0 0" x-text="modal.editing ? 'Mettre à jour les informations' : 'Créer un compte sur la plateforme'"></p>
        </div>
        <button @click="modal.open=false" style="margin-left:auto;width:28px;height:28px;border-radius:7px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--mj-ink-3)">
          <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 4l12 12M16 4L4 16"/></svg>
        </button>
      </div>

      {{-- Modal form --}}
      <div style="padding:22px 24px 24px">
        <template x-if="!modal.editing">
          <form :action="'{{ route('admin.users.store') }}'" method="POST" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            @include('admin.users._form_fields', ['editing' => false])
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" class="adm-cta" style="flex:1;justify-content:center">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
                Créer l'utilisateur
              </button>
              <button type="button" @click="modal.open=false" style="height:42px;padding:0 16px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:500 13px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">Annuler</button>
            </div>
          </form>
        </template>
        <template x-if="modal.editing">
          <form :action="'/admin/users/'+modal.userId" method="POST" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            <input type="hidden" name="_method" value="PATCH"/>
            @include('admin.users._form_fields', ['editing' => true])
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" class="adm-cta" style="flex:1;justify-content:center">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
                Enregistrer
              </button>
              <button type="button" @click="modal.open=false" style="height:42px;padding:0 16px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--surface-2);font:500 13px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">Annuler</button>
            </div>
          </form>
        </template>
      </div>
    </div>
  </div>

</div>{{-- /x-data --}}

<script>
function usersPage() {
  return {
    modal: { open: false, editing: false, userId: null, name: '', email: '', role: 'student', schoolId: '', isActive: true },
    init() {
      @if(session('errors') && session('errors')->any())
      this.modal.open = true;
      @endif
    },
    openCreate(role = 'student', schoolId = '') {
      this.modal = { open: true, editing: false, userId: null, name: '', email: '', role: role, schoolId: schoolId ? String(schoolId) : '', isActive: true };
      this.$nextTick(() => this.bindFields());
    },
    openEdit(id, name, email, role, schoolId, isActive) {
      this.modal = { open: true, editing: true, userId: id, name, email, role, schoolId: schoolId ? String(schoolId) : '', isActive };
      this.$nextTick(() => this.bindFields());
    },
    bindFields() {
      const f = document.querySelector('[x-data="usersPage()"] form');
      if (!f) return;
      const set = (n, v) => { const el = f.querySelector('[name="'+n+'"]'); if(el){ el.value = v ?? ''; } };
      set('name',       this.modal.name);
      set('email',      this.modal.email);
      set('role',       this.modal.role);
      set('school_id',  this.modal.schoolId);
      const active = f.querySelector('[name="is_active"][type="checkbox"]');
      if (active) active.checked = this.modal.isActive;
      const pwd = f.querySelector('[name="password"]');
      if (pwd) pwd.value = '';
    }
  }
}
</script>
</x-layouts.admin>
