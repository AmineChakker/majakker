<x-layouts.app title="Élèves" :subtitle="$students->total().' inscrits'">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
    <h2 class="serif" style="font:400 32px/1 var(--f-display)">Élèves</h2>
    <span class="eyebrow">{{ number_format($students->total()) }} inscrits</span>
  </div>
  <div style="overflow:auto">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="border-bottom:0.5px solid var(--line)">
          @foreach(['Élève','Email','Classes','Publications','Statut','Inscrit le'] as $h)
          <th style="text-align:left;padding:8px 12px;font:500 10px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3)">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($students as $s)
        <tr style="border-bottom:0.5px solid var(--line);transition:background .14s" onmouseenter="this.style.background='var(--surface-2)'" onmouseleave="this.style.background=''">
          <td style="padding:12px">
            <div style="display:flex;align-items:center;gap:12px">
              <x-ui.avatar :name="$s->name" size="32"/>
              <div>
                <div style="font:500 13px/1.2 var(--f-ui)">{{ $s->name }}</div>
                <div style="font:400 10.5px/1 var(--f-mono);color:var(--ink-4);margin-top:2px">@{{ $s->handle }}</div>
              </div>
            </div>
          </td>
          <td style="padding:12px;font:400 12px/1 var(--f-ui);color:var(--ink-2)">{{ $s->email }}</td>
          <td style="padding:12px">
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @forelse($s->groups->where('kind','class')->take(2) as $g)
              <span class="chip" style="font-size:10px;height:18px;border-left:2px solid var(--c-{{ $g->color }})">{{ $g->name }}</span>
              @empty
              <span style="font:400 11px/1 var(--f-ui);color:var(--ink-4)">—</span>
              @endforelse
            </div>
          </td>
          <td style="padding:12px;font:500 12px/1 var(--f-mono)">{{ $s->posts()->count() }}</td>
          <td style="padding:12px">
            <span class="chip {{ $s->is_active ? 'chip-atlas' : '' }}" style="font-size:9.5px">{{ $s->is_active ? 'Actif' : 'Inactif' }}</span>
          </td>
          <td style="padding:12px;font:400 11.5px/1 var(--f-mono);color:var(--ink-3)">{{ $s->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="margin-top:20px">{{ $students->links() }}</div>
</div>
</x-layouts.app>
