<x-layouts.app title="Enseignants" :subtitle="$teachers->total().' membres'">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
    <h2 class="serif" style="font:400 32px/1 var(--f-display)">Enseignants</h2>
    <span class="eyebrow">{{ $teachers->total() }} membres actifs</span>
  </div>
  <div style="overflow:auto">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="border-bottom:0.5px solid var(--line)">
          @foreach(['Enseignant','Email','Matières / Classes','Statut','Depuis'] as $h)
          <th style="text-align:left;padding:8px 12px;font:500 10px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3)">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($teachers as $t)
        <tr style="border-bottom:0.5px solid var(--line);transition:background .14s" onmouseenter="this.style.background='var(--surface-2)'" onmouseleave="this.style.background=''">
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
            <span class="chip {{ $t->is_active ? 'chip-atlas' : '' }}" style="font-size:9.5px">{{ $t->is_active ? 'Actif' : 'Inactif' }}</span>
          </td>
          <td style="padding:12px;font:400 11.5px/1 var(--f-mono);color:var(--ink-3)">{{ $t->created_at->format('M Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="margin-top:20px">{{ $teachers->links() }}</div>
</div>
</x-layouts.app>
