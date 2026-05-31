<x-layouts.app title="Enseignants" :subtitle="$teachers->total().' membres'">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll"
     x-data="teachersPage()" x-init="init()">

  @if(session('success'))
  <div style="margin-bottom:20px;padding:13px 16px;border-radius:10px;background:rgba(16,185,129,.08);border:0.5px solid rgba(16,185,129,.25);color:#047857;font:500 13px/1 var(--f-ui)">{{ session('success') }}</div>
  @endif
  @if(session('error'))
  <div style="margin-bottom:20px;padding:13px 16px;border-radius:10px;background:rgba(220,38,38,.07);border:0.5px solid rgba(220,38,38,.2);color:#B91C1C;font:500 13px/1 var(--f-ui)">{{ session('error') }}</div>
  @endif

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
    <h2 class="serif" style="font:400 32px/1 var(--f-display)">Enseignants</h2>
    <div style="display:flex;align-items:center;gap:12px">
      <span class="eyebrow">{{ $teachers->total() }} membres</span>
      <button @click="openCreate()" style="display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;border-radius:999px;border:0.5px solid var(--line-2);background:var(--surface);font:500 12.5px/1 var(--f-ui);color:var(--ink);box-shadow:var(--sh-sm);cursor:pointer">
        <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
        Ajouter
      </button>
    </div>
  </div>

  <div style="overflow:auto">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="border-bottom:0.5px solid var(--line)">
          @foreach(['Enseignant','Email','Matières / Classes','Statut','Depuis',''] as $h)
          <th style="text-align:left;padding:8px 12px;font:500 10px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3)">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($teachers as $t)
        <tr style="border-bottom:0.5px solid var(--line);transition:background .14s"
            onmouseenter="this.style.background='var(--surface-2)';this.querySelector('.row-actions').style.opacity='1'"
            onmouseleave="this.style.background='';this.querySelector('.row-actions').style.opacity='0'">
          <td style="padding:12px">
            <div style="display:flex;align-items:center;gap:12px">
              <x-ui.avatar :name="$t->name" size="32"/>
              <div>
                <div style="font:500 13px/1.2 var(--f-ui)">{{ $t->name }}</div>
                @if($t->location)<div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:2px">{{ $t->location }}</div>@endif
              </div>
            </div>
          </td>
          <td style="padding:12px;font:400 12px/1 var(--f-ui);color:var(--ink-2)">{{ $t->email }}</td>
          <td style="padding:12px">
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @forelse($t->groups->take(3) as $g)
              <span class="chip" style="font-size:10px;height:18px">{{ $g->name }}</span>
              @empty
              <span style="font:400 11px/1 var(--f-ui);color:var(--ink-4)">—</span>
              @endforelse
              @if($t->groups->count() > 3)
              <span style="font:400 11px/1 var(--f-mono);color:var(--ink-3)">+{{ $t->groups->count()-3 }}</span>
              @endif
            </div>
          </td>
          <td style="padding:12px">
            <span style="display:inline-flex;align-items:center;gap:4px;height:18px;padding:0 8px;border-radius:999px;font:600 9.5px/1 var(--f-mono);
              {{ $t->is_active ? 'background:rgba(16,185,129,.10);color:#047857' : 'background:rgba(220,38,38,.08);color:#B91C1C' }}">
              <span style="width:4px;height:4px;border-radius:999px;background:currentColor"></span>
              {{ $t->is_active ? 'Actif' : 'Inactif' }}
            </span>
          </td>
          <td style="padding:12px;font:400 11.5px/1 var(--f-mono);color:var(--ink-3)">{{ $t->created_at->format('M Y') }}</td>
          <td style="padding:12px">
            <div class="row-actions" style="display:flex;gap:5px;opacity:0;transition:opacity .15s">
              <button @click="openEdit({{ $t->id }}, '{{ addslashes($t->name) }}', '{{ addslashes($t->email) }}')"
                      style="height:28px;padding:0 10px;border-radius:7px;border:0.5px solid var(--line-2);background:var(--surface);font:500 11px/1 var(--f-ui);color:var(--ink-2);cursor:pointer">
                Modifier
              </button>
              <form action="{{ route('dashboard.teachers.destroy', $t) }}" method="POST"
                    onsubmit="return confirm('Supprimer {{ addslashes($t->name) }} ?')">
                @csrf @method('DELETE')
                <button type="submit" style="height:28px;padding:0 10px;border-radius:7px;border:0.5px solid rgba(220,38,38,.2);background:rgba(220,38,38,.05);font:500 11px/1 var(--f-ui);color:#B91C1C;cursor:pointer">
                  Supprimer
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="margin-top:20px">{{ $teachers->links() }}</div>

  {{-- Create / Edit modal --}}
  <div x-show="modal.open" x-cloak
       style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:20px"
       @click.self="modal.open=false">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(4px)" @click="modal.open=false"></div>
    <div style="position:relative;width:100%;max-width:520px;background:var(--surface);border-radius:18px;border:0.5px solid var(--line);box-shadow:0 32px 80px -20px rgba(20,21,43,.25);overflow:hidden">

      <div style="display:flex;align-items:center;gap:12px;padding:22px 24px;border-bottom:0.5px solid var(--line)">
        <div style="width:36px;height:36px;border-radius:10px;background:var(--ink);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <x-ui.icon name="users" size="16" style="color:var(--bg)"/>
        </div>
        <div>
          <h3 style="font:400 20px/1 var(--f-display);margin:0" x-text="modal.editing ? 'Modifier l\'enseignant' : 'Nouvel enseignant'"></h3>
          <p style="font:400 11.5px/1 var(--f-ui);color:var(--ink-3);margin:4px 0 0" x-text="modal.editing ? 'Mettre à jour les informations' : 'Créer un compte enseignant'"></p>
        </div>
        <button @click="modal.open=false" style="margin-left:auto;width:28px;height:28px;border-radius:7px;border:0.5px solid var(--line-2);background:var(--surface-2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ink-3)">
          <svg width="11" height="11" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 4l12 12M16 4L4 16"/></svg>
        </button>
      </div>

      <div style="padding:22px 24px 24px">
        <template x-if="!modal.editing">
          <form action="{{ route('dashboard.teachers.store') }}" method="POST" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div style="grid-column:1/-1">
                <label style="font:500 11.5px/1 var(--f-ui);color:var(--ink-2);display:block;margin-bottom:5px">Nom complet *</label>
                <input name="name" required placeholder="Yasmine Bennani"
                       style="width:100%;height:40px;padding:0 12px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--ink);outline:none;box-sizing:border-box"
                       :value="modal.name" x-model="modal.name"/>
              </div>
              <div>
                <label style="font:500 11.5px/1 var(--f-ui);color:var(--ink-2);display:block;margin-bottom:5px">Adresse email *</label>
                <input name="email" type="email" required placeholder="yasmine@majakker.ma"
                       style="width:100%;height:40px;padding:0 12px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--ink);outline:none;box-sizing:border-box"
                       :value="modal.email" x-model="modal.email"/>
              </div>
              <div>
                <label style="font:500 11.5px/1 var(--f-ui);color:var(--ink-2);display:block;margin-bottom:5px">Mot de passe *</label>
                <div style="position:relative">
                  <input name="password" type="password" id="modal-pwd-t" required
                         placeholder="8 caractères minimum"
                         style="width:100%;height:40px;padding:0 36px 0 12px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--ink);outline:none;box-sizing:border-box"/>
                  <button type="button"
                          onclick="var i=document.getElementById('modal-pwd-t');i.type=i.type==='password'?'text':'password'"
                          style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:0;cursor:pointer;color:var(--ink-3);display:flex;align-items:center;padding:4px">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
                      <path d="M2 10 C4 6 7 4 10 4 C13 4 16 6 18 10 C16 14 13 16 10 16 C7 16 4 14 2 10Z"/>
                      <circle cx="10" cy="10" r="2.5"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;flex:1;height:42px;padding:0 16px;border-radius:10px;border:0.5px solid var(--ink);background:var(--ink);font:500 13px/1 var(--f-ui);color:var(--bg);cursor:pointer;box-shadow:var(--sh-sm)">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M10 4v12M4 10h12"/></svg>
                Créer l'enseignant
              </button>
              <button type="button" @click="modal.open=false" style="height:42px;padding:0 16px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface-2);font:500 13px/1 var(--f-ui);color:var(--ink-2);cursor:pointer">Annuler</button>
            </div>
          </form>
        </template>

        <template x-if="modal.editing">
          <form :action="'/dashboard/teachers/'+modal.userId" method="POST" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div style="grid-column:1/-1">
                <label style="font:500 11.5px/1 var(--f-ui);color:var(--ink-2);display:block;margin-bottom:5px">Nom complet *</label>
                <input name="name" required placeholder="Yasmine Bennani"
                       style="width:100%;height:40px;padding:0 12px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--ink);outline:none;box-sizing:border-box"
                       :value="modal.name" x-model="modal.name"/>
              </div>
              <div>
                <label style="font:500 11.5px/1 var(--f-ui);color:var(--ink-2);display:block;margin-bottom:5px">Adresse email *</label>
                <input name="email" type="email" required placeholder="yasmine@majakker.ma"
                       style="width:100%;height:40px;padding:0 12px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--ink);outline:none;box-sizing:border-box"
                       :value="modal.email" x-model="modal.email"/>
              </div>
              <div>
                <label style="font:500 11.5px/1 var(--f-ui);color:var(--ink-2);display:block;margin-bottom:5px">Nouveau mot de passe</label>
                <div style="position:relative">
                  <input name="password" type="password" id="modal-pwd-e"
                         placeholder="Laisser vide pour conserver"
                         style="width:100%;height:40px;padding:0 36px 0 12px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--ink);outline:none;box-sizing:border-box"/>
                  <button type="button"
                          onclick="var i=document.getElementById('modal-pwd-e');i.type=i.type==='password'?'text':'password'"
                          style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:0;cursor:pointer;color:var(--ink-3);display:flex;align-items:center;padding:4px">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
                      <path d="M2 10 C4 6 7 4 10 4 C13 4 16 6 18 10 C16 14 13 16 10 16 C7 16 4 14 2 10Z"/>
                      <circle cx="10" cy="10" r="2.5"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:4px">
              <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;flex:1;height:42px;padding:0 16px;border-radius:10px;border:0.5px solid var(--ink);background:var(--ink);font:500 13px/1 var(--f-ui);color:var(--bg);cursor:pointer;box-shadow:var(--sh-sm)">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
                Enregistrer
              </button>
              <button type="button" @click="modal.open=false" style="height:42px;padding:0 16px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface-2);font:500 13px/1 var(--f-ui);color:var(--ink-2);cursor:pointer">Annuler</button>
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
    modal: { open: false, editing: false, userId: null, name: '', email: '' },
    init() {
      @if(session('errors') && session('errors')->any())
      this.modal.open = true;
      @endif
    },
    openCreate() {
      this.modal = { open: true, editing: false, userId: null, name: '', email: '' };
      this.$nextTick(() => this.bindFields());
    },
    openEdit(id, name, email) {
      this.modal = { open: true, editing: true, userId: id, name, email };
      this.$nextTick(() => this.bindFields());
    },
    bindFields() {
      const f = document.querySelector('[x-data="teachersPage()"] form');
      if (!f) return;
      const set = (n, v) => { const el = f.querySelector('[name="'+n+'"]'); if(el) el.value = v ?? ''; };
      set('name', this.modal.name);
      set('email', this.modal.email);
      const pwd = f.querySelector('[name="password"]');
      if (pwd) pwd.value = '';
    }
  }
}
</script>
</x-layouts.app>
