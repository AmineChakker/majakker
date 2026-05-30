<x-layouts.admin title="Utilisateurs" subtitle="Plateforme">
<div style="padding:28px 32px;height:100%;overflow:auto" class="scroll">
  <table style="width:100%;border-collapse:collapse">
    <thead><tr style="border-bottom:0.5px solid var(--line)">
      @foreach(['Nom','Email','Rôle','École','Inscrit le'] as $h)
      <th style="text-align:left;padding:8px 12px;font:500 10px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3)">{{ $h }}</th>
      @endforeach
    </tr></thead>
    <tbody>
      @foreach($users as $u)
      <tr style="border-bottom:0.5px solid var(--line)">
        <td style="padding:12px"><div style="display:flex;align-items:center;gap:10px"><x-ui.avatar :name="$u->name" size="28"/><span style="font:500 13px/1.2 var(--f-ui)">{{ $u->name }}</span></div></td>
        <td style="padding:12px;font:400 12px/1 var(--f-ui);color:var(--ink-2)">{{ $u->email }}</td>
        <td style="padding:12px"><span class="chip">{{ $u->role_label }}</span></td>
        <td style="padding:12px;font:400 12px/1 var(--f-ui);color:var(--ink-2)">{{ $u->school?->name ?? '—' }}</td>
        <td style="padding:12px;font:400 12px/1 var(--f-mono);color:var(--ink-3)">{{ $u->created_at->format('d M Y') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div style="margin-top:20px">{{ $users->links() }}</div>
</div>
</x-layouts.admin>
