@if(session('success'))
<div style="margin-bottom:20px;padding:13px 16px;border-radius:10px;background:rgba(16,185,129,.08);border:0.5px solid rgba(16,185,129,.25);color:#047857;font:500 13px/1.3 var(--f-ui)">
  {{ session('success') }}
</div>
@endif

@if(session('new_teacher_email') || session('new_student_email'))
@php $credEmail = session('new_teacher_email') ?? session('new_student_email'); $credPwd = session('new_teacher_password') ?? session('new_student_password'); @endphp
<div style="margin-bottom:20px;padding:18px 20px;border-radius:12px;background:linear-gradient(135deg,rgba(126,91,239,.08),rgba(37,99,235,.06));border:0.5px solid rgba(126,91,239,.22);display:flex;gap:16px;align-items:flex-start">
  <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
    <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><path d="M4 10l5 5L16 6"/></svg>
  </div>
  <div>
    <div style="font:600 13px/1.3 var(--f-ui);color:var(--ink);margin-bottom:8px">Compte créé — transmettez ces identifiants</div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <div style="padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.7);border:0.5px solid rgba(126,91,239,.2)">
        <div style="font:500 9px/1 var(--f-mono);letter-spacing:.14em;text-transform:uppercase;color:var(--ink-3);margin-bottom:4px">Email</div>
        <div style="font:500 13px/1 var(--f-mono);color:var(--ink)">{{ $credEmail }}</div>
      </div>
      <div style="padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.7);border:0.5px solid rgba(126,91,239,.2)">
        <div style="font:500 9px/1 var(--f-mono);letter-spacing:.14em;text-transform:uppercase;color:var(--ink-3);margin-bottom:4px">Mot de passe</div>
        <div style="font:500 13px/1 var(--f-mono);color:var(--ink)">{{ $credPwd }}</div>
      </div>
    </div>
  </div>
</div>
@endif

@if($errors->any())
<div style="margin-bottom:20px;padding:12px 16px;border-radius:10px;background:rgba(220,38,38,.07);border:0.5px solid rgba(220,38,38,.2);color:#B91C1C;font:400 13px/1.4 var(--f-ui)">
  {{ $errors->first() }}
</div>
@endif
