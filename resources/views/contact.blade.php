<x-layouts.guest title="Demander une démo — Majakker">
<style>
/* ── Brand palette ── */
.ct-root{
  --mj-purple:#7E5BEF;--mj-blue:#2563EB;--mj-ink:#14152B;--mj-ink-2:#3F4360;
  --mj-ink-3:#7A7E96;--mj-bg:#FAFAFC;--mj-surface:#FFFFFF;
  --mj-line:rgba(20,21,43,.08);--mj-line-2:rgba(20,21,43,.14);
  --mj-gradient:linear-gradient(135deg,#7E5BEF 0%,#2563EB 100%);
  --sh-sm:0 1px 2px rgba(20,21,43,.06);
  --sh-lg:0 24px 60px -20px rgba(20,21,43,.18);
  background:var(--mj-bg);color:var(--mj-ink);font-family:var(--f-ui);min-height:100vh;
}
@keyframes ct-up{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
@keyframes ct-breathe{0%,100%{opacity:.5}50%{opacity:.8}}

/* ── Grain ── */
.ct-grain{position:fixed;inset:0;z-index:200;pointer-events:none;opacity:.04;mix-blend-mode:multiply;
  background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.92' numOctaves='2' stitchTiles='stitch'/></filter><rect width='180' height='180' filter='url(%23n)'/></svg>")}

/* ── Nav ── */
.ct-nav{position:sticky;top:0;z-index:60;display:flex;align-items:center;gap:20px;
  padding:14px 56px;
  background:color-mix(in oklab,var(--mj-bg) 80%,transparent);
  backdrop-filter:blur(18px) saturate(180%);
  border-bottom:.5px solid var(--mj-line)}

/* ── Layout ── */
.ct-shell{display:grid;grid-template-columns:1fr 1fr;min-height:calc(100vh - 57px)}
.ct-left{position:relative;background:var(--mj-ink);overflow:hidden;
  padding:72px 64px;display:flex;flex-direction:column;justify-content:space-between}
.ct-right{padding:64px 72px;display:flex;flex-direction:column;justify-content:center;
  background:var(--mj-bg);overflow-y:auto}

/* ── Orbs on dark left ── */
.ct-orb-a{position:absolute;top:-100px;right:-100px;width:440px;height:440px;
  background:linear-gradient(135deg,#7E5BEF,#2563EB);border-radius:50%;
  filter:blur(55px);opacity:.55;pointer-events:none}
.ct-orb-b{position:absolute;bottom:-80px;left:-80px;width:360px;height:360px;
  background:linear-gradient(135deg,#8B5CF6,#0EA5E9);border-radius:50%;
  filter:blur(50px);opacity:.32;pointer-events:none}

/* ── Eyebrow ── */
.ct-eyebrow{font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;
  display:inline-flex;align-items:center;gap:12px}
.ct-eyebrow::before{content:'';width:28px;height:.5px;background:currentColor}

/* ── Form ── */
.ct-label{font:500 12.5px/1 var(--f-ui);color:var(--mj-ink-2);display:block;margin-bottom:6px}
.ct-input{width:100%;height:44px;padding:0 14px;border-radius:10px;
  border:.5px solid var(--mj-line-2);background:var(--mj-surface);
  font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;
  transition:border-color .18s,box-shadow .18s;box-sizing:border-box}
.ct-input:focus{border-color:var(--mj-purple);box-shadow:0 0 0 3px rgba(126,91,239,.12)}
.ct-textarea{height:auto;padding:11px 14px;resize:none;line-height:1.55}
.ct-select{appearance:none;cursor:pointer;background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 20 20' fill='none' stroke='%237A7E96' stroke-width='1.5' stroke-linecap='round'><path d='M5 8l5 5 5-5'/></svg>");background-repeat:no-repeat;background-position:right 12px center}
.ct-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;height:50px;
  border-radius:12px;background:var(--mj-gradient);color:#fff;border:0;cursor:pointer;
  font:600 14px/1 var(--f-ui);letter-spacing:-.005em;
  box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 8px 24px -6px rgba(94,57,224,.45);
  transition:transform .2s,filter .2s}
.ct-btn:hover{transform:translateY(-1px);filter:brightness(1.08)}
.ct-btn:disabled{opacity:.6;cursor:not-allowed;transform:none}

/* ── Success ── */
.ct-success{display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:20px;text-align:center;padding:40px 20px;animation:ct-up .6s cubic-bezier(.2,.7,.3,1) both}

/* ── Stat cards ── */
.ct-stat{display:flex;flex-direction:column;gap:4px}

/* ── Feature list ── */
.ct-feat{display:flex;align-items:flex-start;gap:12px;padding:14px 0;border-top:.5px solid rgba(255,255,255,.1)}
.ct-feat:first-child{border-top:0}
.ct-feat-icon{width:32px;height:32px;border-radius:9px;background:rgba(255,255,255,.1);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}

@media(max-width:860px){
  .ct-shell{grid-template-columns:1fr}
  .ct-left{display:none}
  .ct-right{padding:40px 28px}
  .ct-nav{padding:14px 22px}
}
</style>

<div class="ct-root">
<div class="ct-grain"></div>

{{-- Nav --}}
<nav class="ct-nav">
  <a href="{{ route('home') }}" style="display:inline-flex;align-items:center">
    <img src="{{ asset('images/logo.png') }}" alt="Majakker" style="height:38px;width:auto;display:block"/>
  </a>
  <span style="flex:1"></span>
  <a href="{{ route('login') }}" style="font:500 13px/1 var(--f-ui);color:var(--mj-ink-2);text-decoration:none;transition:color .18s"
     onmouseenter="this.style.color='var(--mj-ink)'" onmouseleave="this.style.color='var(--mj-ink-2)'">
    Connexion
  </a>
</nav>

{{-- Shell --}}
<div class="ct-shell">

  {{-- ── LEFT: brand panel ── --}}
  <div class="ct-left">
    <div class="ct-orb-a"></div>
    <div class="ct-orb-b"></div>

    <div style="position:relative;animation:ct-up .8s cubic-bezier(.2,.7,.3,1) backwards .1s">
      <div class="ct-eyebrow" style="color:rgba(250,247,242,.55);margin-bottom:28px">
        Majakker · La démo
      </div>
      <h1 style="font:400 clamp(38px,4.5vw,58px)/.97 var(--f-display);letter-spacing:-.03em;color:#fff;margin:0 0 24px">
        Votre école,<br>
        <span style="background:linear-gradient(135deg,#C7B6FF,#93C5FD);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic">connectée</span><br>
        en 30 jours.
      </h1>
      <p style="font:400 15px/1.65 var(--f-ui);color:rgba(250,247,242,.68);margin:0;max-width:400px">
        Réservez une session de démonstration gratuite avec notre équipe à Rabat. Aucune carte bancaire requise.
      </p>

      {{-- Stats --}}
      <div style="display:flex;gap:28px;margin-top:40px;padding-top:28px;border-top:.5px solid rgba(255,255,255,.12)">
        @foreach([['142','écoles partenaires'],['86 K','élèves actifs'],['30 j','délai de migration']] as [$n,$l])
        <div class="ct-stat">
          <span style="font:400 26px/1 var(--f-display);letter-spacing:-.03em;color:#fff">{{ $n }}</span>
          <span style="font:400 11px/1.3 var(--f-ui);color:rgba(250,247,242,.55);margin-top:5px">{{ $l }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Feature list --}}
    <div style="position:relative;animation:ct-up .8s cubic-bezier(.2,.7,.3,1) backwards .2s">
      @foreach([
        ['<path d="M4 10l5 5L16 6"/>', 'Migration accompagnée', 'Depuis WhatsApp, Drive ou MySchool — sans perte de données.'],
        ['<path d="M12 3L17 6V12a7 7 0 01-7 6 7 7 0 01-7-6V6z"/><path d="M8 10l2 2 4-4"/>', 'Modération IA bilingue', 'Comprend le français et la darija. Alerte sans bloquer la classe.'],
        ['<circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>', 'Support 6 mois inclus', 'Formation en présentiel + assistance continue, sans surcoût.'],
      ] as [$icon,$title,$desc])
      <div class="ct-feat">
        <div class="ct-feat-icon">
          <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="1.5" stroke-linecap="round">{!! $icon !!}</svg>
        </div>
        <div>
          <div style="font:600 13px/1.2 var(--f-ui);color:#fff;margin-bottom:3px">{{ $title }}</div>
          <div style="font:400 12px/1.45 var(--f-ui);color:rgba(250,247,242,.55)">{{ $desc }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ── RIGHT: form ── --}}
  <div class="ct-right">
    <div style="max-width:460px;width:100%;margin:0 auto;animation:ct-up .7s cubic-bezier(.2,.7,.3,1) backwards .15s">

      @if(session('success'))
      {{-- ── Success state ── --}}
      <div class="ct-success">
        <div style="width:72px;height:72px;border-radius:20px;background:linear-gradient(135deg,#7E5BEF,#2563EB);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 12px 32px -8px rgba(94,57,224,.5)">
          <svg width="32" height="32" viewBox="0 0 20 20" fill="none" stroke="#fff" stroke-width="1.6" stroke-linecap="round">
            <path d="M4 10l5 5L16 6"/>
          </svg>
        </div>
        <div>
          <h2 style="font:400 32px/1.05 var(--f-display);letter-spacing:-.02em;color:var(--mj-ink);margin:0 0 10px">
            Merci, {{ session('contact_name') }} !
          </h2>
          <p style="font:400 15px/1.6 var(--f-ui);color:var(--mj-ink-3);margin:0;max-width:360px">
            Votre demande a bien été reçue. Notre équipe vous contactera sous <strong style="color:var(--mj-ink-2)">24 heures</strong> pour planifier votre démo.
          </p>
        </div>
        <div style="padding:16px 20px;border-radius:12px;background:rgba(126,91,239,.07);border:.5px solid rgba(126,91,239,.18);
                    font:400 12.5px/1.5 var(--f-mono);color:var(--mj-ink-3);text-align:center;letter-spacing:.04em">
          CONTACT@MAJAKKER.MA · RABAT, MAROC
        </div>
        <a href="{{ route('home') }}"
           style="display:inline-flex;align-items:center;gap:7px;height:42px;padding:0 20px;border-radius:10px;
                  border:.5px solid var(--mj-line-2);background:var(--mj-surface);
                  font:500 13px/1 var(--f-ui);color:var(--mj-ink-2);text-decoration:none;transition:all .18s"
           onmouseenter="this.style.borderColor='var(--mj-purple)';this.style.color='var(--mj-purple)'"
           onmouseleave="this.style.borderColor='var(--mj-line-2)';this.style.color='var(--mj-ink-2)'">
          ← Retour à l'accueil
        </a>
      </div>

      @else
      {{-- ── Form ── --}}
      <div style="margin-bottom:32px">
        <div class="ct-eyebrow" style="color:var(--mj-ink-3);margin-bottom:16px">Demande de démo gratuite</div>
        <h2 style="font:400 34px/1.05 var(--f-display);letter-spacing:-.02em;color:var(--mj-ink);margin:0 0 8px">
          Parlons de votre école.
        </h2>
        <p style="font:400 14.5px/1.55 var(--f-ui);color:var(--mj-ink-3);margin:0">
          Remplissez ce formulaire et nous vous recontactons sous 24 h.
        </p>
      </div>

      @if($errors->any())
      <div style="padding:12px 16px;border-radius:10px;background:rgba(220,38,38,.07);border:.5px solid rgba(220,38,38,.2);
                  color:#B91C1C;font:400 13px/1.4 var(--f-ui);margin-bottom:20px">
        {{ $errors->first() }}
      </div>
      @endif

      <form action="{{ route('contact.store') }}" method="POST" style="display:flex;flex-direction:column;gap:16px">
        @csrf

        {{-- Name --}}
        <div>
          <label class="ct-label">Votre nom complet *</label>
          <input name="name" value="{{ old('name') }}" required placeholder="Najat Tazi"
                 class="ct-input"/>
        </div>

        {{-- School + City --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label class="ct-label">Nom de l'école *</label>
            <input name="school" value="{{ old('school') }}" required placeholder="Lycée Majakker"
                   class="ct-input"/>
          </div>
          <div>
            <label class="ct-label">Ville *</label>
            <input name="city" value="{{ old('city') }}" required placeholder="Casablanca"
                   class="ct-input"/>
          </div>
        </div>

        {{-- Email + Phone --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label class="ct-label">Adresse e-mail *</label>
            <input name="email" type="email" value="{{ old('email') }}" required placeholder="direction@ecole.ma"
                   class="ct-input"/>
          </div>
          <div>
            <label class="ct-label">Téléphone</label>
            <input name="phone" value="{{ old('phone') }}" placeholder="+212 6 00 00 00 00"
                   class="ct-input"/>
          </div>
        </div>

        {{-- Number of students --}}
        <div>
          <label class="ct-label">Nombre d'élèves</label>
          <select name="students" class="ct-input ct-select">
            <option value="" {{ !old('students') ? 'selected' : '' }}>— Sélectionner</option>
            @foreach(['Moins de 200','200 – 500','500 – 1 000','1 000 – 2 000','Plus de 2 000'] as $opt)
            <option value="{{ $opt }}" {{ old('students')===$opt?'selected':'' }}>{{ $opt }}</option>
            @endforeach
          </select>
        </div>

        {{-- Message --}}
        <div>
          <label class="ct-label">Message (optionnel)</label>
          <textarea name="message" rows="3" placeholder="Parlez-nous de vos besoins, de votre contexte, de vos questions…"
                    class="ct-input ct-textarea" style="height:90px">{{ old('message') }}</textarea>
        </div>

        {{-- Submit --}}
        <button type="submit" class="ct-btn" style="margin-top:4px">
          Réserver ma démo gratuite
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
            <path d="M5 10h10M12 6l4 4-4 4"/>
          </svg>
        </button>

        <p style="font:400 11px/1.5 var(--f-mono);color:var(--mj-ink-3);text-align:center;letter-spacing:.05em;margin:0">
          AUCUNE CARTE BANCAIRE · RÉPONSE SOUS 24 H
        </p>
      </form>
      @endif

    </div>
  </div>
</div>
</div>
</x-layouts.guest>
