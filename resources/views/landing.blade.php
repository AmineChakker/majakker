<x-layouts.guest :title="'Majakker — La place publique numérique des écoles marocaines'">
<style>
/* ── Brand palette ── */
.lp-root{
  --mj-purple:#7E5BEF;--mj-purple-deep:#5B3FE6;--mj-blue:#2563EB;
  --mj-ink:#14152B;--mj-ink-2:#3F4360;--mj-ink-3:#7A7E96;--mj-bg:#FAFAFC;--mj-surface:#FFFFFF;
  --mj-line:rgba(20,21,43,.08);--mj-line-2:rgba(20,21,43,.14);
  --mj-gradient:linear-gradient(135deg,#7E5BEF 0%,#2563EB 100%);
  --mj-gradient-soft:linear-gradient(135deg,rgba(126,91,239,.14) 0%,rgba(37,99,235,.14) 100%);
  --mj-gradient-strong:linear-gradient(135deg,#8B5CF6 0%,#1D4ED8 100%);
  --bg:var(--mj-bg);--surface:var(--mj-surface);--surface-2:#F2F2F8;--surface-3:#E6E7F0;
  --ink:var(--mj-ink);--ink-2:var(--mj-ink-2);--ink-3:var(--mj-ink-3);--ink-4:#B7BAC9;
  --line:var(--mj-line);--line-2:var(--mj-line-2);
  --c-blue:#2563EB;--c-blue-soft:#E8EEFE;
  --c-saffron:#7E5BEF;--c-saffron-soft:#EDE7FE;
  --c-terracotta:#EC4899;--c-terracotta-soft:#FCE7F3;
  --c-atlas:#06B6D4;--c-atlas-soft:#DCFAFC;
  --sh-sm:0 1px 0 rgba(26,22,20,.04),0 1px 2px rgba(26,22,20,.04);
  --sh-md:0 1px 0 rgba(255,255,255,.6) inset,0 8px 24px -12px rgba(26,22,20,.10);
  --sh-lg:0 1px 0 rgba(255,255,255,.8) inset,0 24px 60px -20px rgba(26,22,20,.18);
  background:var(--mj-bg);color:var(--mj-ink);font-family:var(--f-ui);
  position:relative;overflow-x:hidden;
}

/* ── Animations ── */
@keyframes lp-fadeup{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
@keyframes lp-marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
@keyframes lp-breathe{0%,100%{opacity:.55}50%{opacity:.85}}
@keyframes lp-float{0%,100%{transform:translateY(0) rotate(var(--rot,0deg))}50%{transform:translateY(-14px) rotate(var(--rot,0deg))}}
@keyframes lp-orbit{from{transform:rotate(0)}to{transform:rotate(360deg)}}

.lp-fadeup{animation:lp-fadeup .9s cubic-bezier(.2,.7,.3,1) backwards}
.lp-d1{animation-delay:.08s}.lp-d2{animation-delay:.18s}
.lp-d3{animation-delay:.28s}.lp-d4{animation-delay:.38s}
.lp-float-a{animation:lp-float 7s ease-in-out infinite;--rot:-2deg}
.lp-float-b{animation:lp-float 9s ease-in-out infinite .8s;--rot:3deg}
.lp-float-c{animation:lp-float 8s ease-in-out infinite .3s;--rot:-4deg}

/* ── Grain ── */
.lp-grain{position:fixed;inset:0;z-index:200;pointer-events:none;opacity:.045;mix-blend-mode:multiply;
  background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.92' numOctaves='2' stitchTiles='stitch'/></filter><rect width='180' height='180' filter='url(%23n)'/></svg>")}

/* ── Section wrapper ── */
.lp-sec{position:relative;max-width:1440px;margin:0 auto;padding:0 56px}

/* ── Eyebrow ── */
.lp-eyebrow{font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;
  color:var(--ink-3);display:inline-flex;align-items:center;gap:12px}
.lp-eyebrow::before{content:'';width:28px;height:0.5px;background:var(--ink-3)}
.lp-eyebrow.on-dark{color:rgba(250,247,242,.6)}
.lp-eyebrow.on-dark::before{background:rgba(250,247,242,.45)}

/* ── Display font ── */
.lp-display{font-family:var(--f-display);font-weight:400;letter-spacing:-.035em;line-height:.97}

/* ── Grad text ── */
.mj-grad-text{background:var(--mj-gradient);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic}

/* ── Cursor glow ── */
.lp-glow{position:absolute;width:560px;height:560px;pointer-events:none;z-index:0;
  left:var(--mx,30%);top:var(--my,28%);transform:translate(-50%,-50%);
  background:radial-gradient(circle,rgba(126,91,239,.30),transparent 60%),
             radial-gradient(circle at 60% 60%,rgba(37,99,235,.22),transparent 70%);
  transition:opacity .3s}

/* ── Mesh ── */
.lp-mesh{position:absolute;inset:0;pointer-events:none;opacity:.85;
  background:radial-gradient(900px circle at 12% 22%,rgba(126,91,239,.28),transparent 55%),
             radial-gradient(800px circle at 88% 8%,rgba(37,99,235,.28),transparent 55%),
             radial-gradient(900px circle at 72% 88%,rgba(139,92,246,.22),transparent 60%);
  animation:lp-breathe 8s ease-in-out infinite}

/* ── Ambient ring on dark ── */
.mj-ring{position:absolute;inset:-40%;pointer-events:none;opacity:.45;
  background:radial-gradient(45% 45% at 18% 22%,rgba(126,91,239,.45),transparent 60%),
             radial-gradient(35% 35% at 82% 18%,rgba(37,99,235,.4),transparent 60%),
             radial-gradient(40% 40% at 70% 82%,rgba(139,92,246,.3),transparent 60%);
  filter:blur(8px)}

/* ── Nav ── */
.lp-nav{position:sticky;top:0;z-index:60;display:flex;align-items:center;gap:28px;padding:16px 56px;
  background:color-mix(in oklab,var(--mj-bg) 72%,transparent);
  -webkit-backdrop-filter:saturate(180%) blur(18px);backdrop-filter:saturate(180%) blur(18px);
  border-bottom:0.5px solid var(--line)}
.lp-nav-link{font:500 13px/1 var(--f-ui);color:var(--ink-2);text-decoration:none;transition:color .2s;position:relative;display:inline-block}
.lp-nav-link::after{content:'';position:absolute;left:0;right:0;bottom:-4px;height:.5px;background:currentColor;transform:scaleX(0);transform-origin:left;transition:transform .35s cubic-bezier(.2,.7,.3,1)}
.lp-nav-link:hover{color:var(--ink)}.lp-nav-link:hover::after{transform:scaleX(1)}

/* ── CTA ── */
.lp-cta{display:inline-flex;align-items:center;gap:8px;height:46px;padding:0 22px;border-radius:999px;
  background:var(--mj-gradient);color:#fff;border:0;cursor:pointer;
  font:500 13.5px/1 var(--f-ui);letter-spacing:-.005em;text-decoration:none;
  box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 8px 24px -6px rgba(94,57,224,.45);
  transition:transform .25s,box-shadow .25s,filter .25s}
.lp-cta:hover{transform:translateY(-1px);filter:brightness(1.08);box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 18px 42px -10px rgba(94,57,224,.55)}
.lp-cta-ghost{background:var(--surface);color:var(--ink);border:0.5px solid var(--line-2);box-shadow:none}
.lp-cta-ghost:hover{background:#fff;border-color:var(--mj-purple);color:var(--mj-purple)}
.lp-cta-light{background:#fff;color:var(--ink);box-shadow:0 1px 0 rgba(0,0,0,.04) inset,0 8px 22px -6px rgba(0,0,0,.35)}
.lp-cta-light:hover{filter:brightness(1.02);transform:translateY(-1px)}

/* ── Marquee ── */
.lp-marquee-wrap{overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 10%,#000 90%,transparent);mask-image:linear-gradient(90deg,transparent,#000 10%,#000 90%,transparent)}
.lp-marquee-track{display:flex;gap:56px;white-space:nowrap;width:max-content;animation:lp-marquee 55s linear infinite}

/* ── Bento ── */
.lp-bento{display:grid;grid-template-columns:repeat(12,1fr);grid-auto-rows:minmax(220px,auto);gap:16px}
.lp-card{position:relative;overflow:hidden;background:var(--surface);border:.5px solid var(--line);border-radius:22px;
  padding:28px;display:flex;flex-direction:column;
  transition:transform .45s cubic-bezier(.2,.7,.3,1),box-shadow .45s,border-color .45s}
.lp-card:hover{transform:translateY(-4px);box-shadow:var(--sh-lg);border-color:var(--line-2)}
.lp-card-dark{background:var(--ink);color:var(--bg);border-color:transparent}
.lp-icon-tile{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;transition:transform .4s cubic-bezier(.2,.7,.3,1)}
.lp-card:hover .lp-icon-tile{transform:rotate(-8deg) scale(1.06)}

/* ── Stats band ── */
.lp-stats-band{position:relative;background:var(--ink);color:var(--bg);border-radius:28px;padding:80px 56px 100px;overflow:hidden}
.lp-stats-band::after{content:'';position:absolute;bottom:0;left:0;right:0;height:220px;pointer-events:none;opacity:.85;
  background:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 220' preserveAspectRatio='xMidYEnd slice'><path d='M0 220 L0 130 L140 78 L260 110 L420 50 L580 95 L740 35 L880 80 L1040 45 L1200 90 L1320 60 L1440 100 L1440 220 Z' fill='rgba(255,255,255,0.07)'/><path d='M0 220 L0 160 L180 120 L340 145 L500 115 L660 140 L820 110 L980 138 L1140 120 L1300 150 L1440 130 L1440 220 Z' fill='rgba(255,255,255,0.04)'/></svg>") bottom/cover no-repeat}

/* ── Testimonial ── */
.lp-qmark{font-family:var(--f-display);font-style:italic;font-size:220px;line-height:.65;
  color:var(--c-blue);opacity:.22;position:absolute;top:-10px;left:-8px;pointer-events:none}

/* ── Process connector ── */
.lp-proc{display:grid;grid-template-columns:repeat(3,1fr);gap:32px;position:relative}
.lp-proc::before{content:'';position:absolute;top:28px;left:8%;right:8%;height:1px;
  background:repeating-linear-gradient(90deg,var(--ink-4) 0 4px,transparent 4px 10px);z-index:0}

/* ── Avatar util ── */
.lp-av{display:inline-flex;align-items:center;justify-content:center;border-radius:8px;
  font-family:var(--f-ui);font-weight:600;letter-spacing:-.01em;flex-shrink:0}

@media(max-width:920px){
  .lp-sec{padding:0 24px}
  .lp-nav{padding:14px 22px;gap:14px;flex-wrap:wrap}
  .lp-bento{grid-template-columns:repeat(6,1fr)}
  .lp-proc{grid-template-columns:1fr}
  .lp-proc::before{display:none}
}
@media(max-width:640px){
  .lp-nav .lp-nav-links{display:none}
}
</style>

<div class="lp-root" id="lp-root">
<div class="lp-grain"></div>

{{-- ════════ NAV ════════ --}}
<nav class="lp-nav">
  <a href="/" style="display:inline-flex;align-items:center">
    <img src="{{ asset('images/logo.png') }}" alt="Majakker" style="height:42px;width:auto;display:block"/>
  </a>
  <div class="lp-nav-links" style="display:flex;gap:26px;margin-left:28px">
    <a href="#bento" class="lp-nav-link">Plateforme</a>
    <a href="#stats" class="lp-nav-link">Écoles</a>
    <a href="#stats" class="lp-nav-link">Élèves</a>
    <a href="#manifeste" class="lp-nav-link">Manifeste</a>
    <a href="#process" class="lp-nav-link">Tarifs</a>
  </div>
  <span style="flex:1"></span>
  <a href="{{ route('login') }}" class="lp-nav-link" style="color:var(--ink-2)">Connexion</a>
  <a href="{{ route('contact') }}" class="lp-cta" style="height:40px;padding:0 18px;font-size:13px">
    Demander une démo
    <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M5 10h10M12 6l4 4-4 4"/></svg>
  </a>
</nav>

{{-- ════════ HERO ════════ --}}
<section id="hero-section" style="position:relative;overflow:hidden" onmousemove="
  var r=this.getBoundingClientRect();
  this.style.setProperty('--mx',(event.clientX-r.left)+'px');
  this.style.setProperty('--my',(event.clientY-r.top)+'px')">

  <div class="lp-mesh"></div>
  <div class="lp-glow"></div>

  {{-- Orb top-right --}}
  <div style="position:absolute;top:-120px;right:-120px;width:520px;height:520px;
              background:var(--mj-gradient);border-radius:50%;
              filter:blur(60px);opacity:.28;pointer-events:none"></div>

  <div class="lp-sec" style="padding-top:110px;padding-bottom:110px;position:relative;z-index:1">
    <div style="display:grid;grid-template-columns:1.15fr 1fr;gap:60px;align-items:center">

      {{-- Left --}}
      <div class="lp-fadeup">
        <div class="lp-eyebrow" style="margin-bottom:20px">Majakker · Learn · Connect · Grow</div>
        <h1 class="lp-display" style="font-size:clamp(56px,8.4vw,108px);margin:0 0 28px">
          La place publique<br>numérique des<br>écoles <span class="mj-grad-text">marocaines</span>.
        </h1>
        <p style="font:400 17px/1.55 var(--f-ui);color:var(--ink-2);max-width:520px;margin:0 0 36px">
          Majakker réunit élèves, enseignants et directions dans un espace social calme et productif. Pensé au Maroc, hébergé au Maroc, parlé en français et en darija — sans algorithme, sans bruit, sans distraction.
        </p>
        <div style="display:flex;gap:12px">
          <a href="{{ route('contact') }}" class="lp-cta">
            Demander une démo
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M5 10h10M12 6l4 4-4 4"/></svg>
          </a>
          <a href="{{ route('contact') }}" class="lp-cta lp-cta-ghost">Voir une école en direct</a>
        </div>
        <div style="display:flex;gap:36px;margin-top:56px;padding-top:28px;border-top:.5px solid var(--line)">
          @foreach([['142','écoles partenaires'],['86 K','élèves actifs'],['4 villes','Casa · Rabat · Marrakech · Tanger']] as [$n,$l])
          <div>
            <div class="lp-display" style="font-size:28px">{{ $n }}</div>
            <div style="font:400 11.5px/1.4 var(--f-ui);color:var(--ink-3);margin-top:6px;max-width:150px">{{ $l }}</div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Right: floating cards --}}
      <div class="lp-fadeup lp-d2" style="position:relative;height:580px">

        {{-- Feed window --}}
        <div class="lp-float-a" style="position:absolute;top:60px;left:0;width:360px;height:440px;
             border-radius:18px;box-shadow:var(--sh-lg);overflow:hidden;background:var(--surface);border:.5px solid var(--line)">
          <div style="padding:10px 14px;border-bottom:.5px solid var(--line);display:flex;align-items:center;gap:8px;background:var(--surface)">
            <span style="width:8px;height:8px;border-radius:999px;background:var(--c-terracotta)"></span>
            <span style="width:8px;height:8px;border-radius:999px;background:var(--c-saffron)"></span>
            <span style="width:8px;height:8px;border-radius:999px;background:var(--c-atlas)"></span>
            <span style="flex:1"></span>
            <span style="font:500 9px/1 var(--f-mono);letter-spacing:.18em;text-transform:uppercase;color:var(--ink-3)">FIL · MAJAKKER</span>
          </div>
          <div style="padding:14px;display:flex;flex-direction:column;gap:14px">
            @foreach([
              ['Najat Tazi','Direction','Semaine des sciences 18–22 mai','La Semaine des sciences débute lundi avec une conférence de Pr. Hassan Aourag. Inscriptions via le lien ci-dessous.',184,23,41],
              ['Karim El Idrissi','Professeur','DM 7 — Suites & récurrence','Voici le DM à rendre pour vendredi. Les exercices 3 et 5 sont prioritaires.',42,18,7],
            ] as [$author,$role,$title,$body,$l,$c,$s])
            <div style="display:grid;grid-template-columns:26px 1fr;gap:10px">
              @php
                $tones=[['#F4EFE6','#5C5346'],['#E8ECFF','#2A3FB8'],['#FAF1DD','#8A6520'],['#F8E7DD','#8E4A2E'],['#DEEFEC','#2D6B61'],['#ECE4D6','#3E342A']];
                $hash=0;foreach(str_split($author) as $ch){$hash=(($hash*31)+ord($ch))&0x7FFFFFFF;}
                [$bg,$fg]=$tones[$hash%6];
                $init=collect(explode(' ',$author))->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))->take(2)->implode('');
              @endphp
              <div class="lp-av" style="width:26px;height:26px;font-size:9px;background:{{ $bg }};color:{{ $fg }}">{{ $init }}</div>
              <div style="display:flex;flex-direction:column;gap:4px;min-width:0">
                <div style="display:flex;align-items:baseline;gap:6px">
                  <span style="font:600 11.5px/1 var(--f-ui)">{{ $author }}</span>
                  <span style="font:400 10px/1 var(--f-ui);color:var(--ink-3)">{{ $role }}</span>
                </div>
                <div style="font:600 12px/1.3 var(--f-ui)">{{ $title }}</div>
                <div style="font:400 11.5px/1.5 var(--f-ui);color:var(--ink-2);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $body }}</div>
                <div style="display:flex;gap:10px;margin-top:2px;color:var(--ink-3);font:500 10.5px/1 var(--f-mono)">
                  <span>♡ {{ $l }}</span><span>○ {{ $c }}</span><span>✦ {{ $s }}</span>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Glass stat card --}}
        <div class="lp-float-b" style="position:absolute;top:0;right:30px;padding:14px 16px;border-radius:16px;width:200px;
             background:rgba(255,255,255,.72);backdrop-filter:blur(18px) saturate(160%);
             border:.5px solid rgba(255,255,255,.9);box-shadow:0 8px 32px -8px rgba(20,21,43,.18)">
          <div class="lp-eyebrow" style="font-size:9.5px">Engagement · S{{ now()->weekOfYear }}</div>
          <div style="display:flex;align-items:baseline;gap:6px;margin-top:6px">
            <span class="lp-display" style="font-size:32px">72%</span>
            <span style="font:500 10px/1 var(--f-mono);color:var(--c-atlas)">+4 PTS</span>
          </div>
          <div style="display:flex;align-items:flex-end;gap:3px;height:30px;margin-top:8px">
            @foreach([3,5,4,6,5,7,8,6,9,8,10,12] as $i => $v)
            <div style="flex:1;height:{{ round(($v/12)*100) }}%;background:{{ $i>=9?'var(--c-blue)':'var(--surface-3)' }};border-radius:1.5px"></div>
            @endforeach
          </div>
        </div>

        {{-- Phone mockup --}}
        <div class="lp-float-c" style="position:absolute;bottom:0;right:0;width:210px;height:400px;
             border-radius:30px;background:var(--ink);padding:6px;box-shadow:var(--sh-lg)">
          <div style="width:100%;height:100%;border-radius:24px;background:var(--surface-2);overflow:hidden;display:flex;flex-direction:column">
            <div style="padding:28px 14px 8px;display:flex;align-items:center;gap:8px">
              <span style="font:500 11px/1 var(--f-ui)">{{ now()->format('H:i') }}</span>
              <span style="flex:1"></span>
              <span style="width:22px;height:7px;border-radius:4px;background:var(--ink-3)"></span>
            </div>
            <div style="padding:0 14px;flex:1;display:flex;flex-direction:column;gap:10px">
              <div class="lp-display" style="font-size:22px">Bonjour<br>Yasmine.</div>
              <div style="background:var(--surface);border-radius:12px;border:.5px solid var(--line);padding:10px">
                <div class="lp-eyebrow" style="font-size:8.5px">Aujourd'hui</div>
                <div style="font:500 11px/1.2 var(--f-ui);margin-top:4px">3 nouvelles publications</div>
              </div>
              <div style="background:var(--surface);border-radius:12px;border:.5px solid var(--line);padding:10px;display:flex;gap:8px">
                <div class="lp-av" style="width:22px;height:22px;font-size:8px;background:#E8ECFF;color:#2A3FB8;flex-shrink:0">KE</div>
                <div style="display:flex;flex-direction:column;flex:1;min-width:0">
                  <span style="font:600 10px/1 var(--f-ui)">K. El Idrissi</span>
                  <span style="font:400 9.5px/1.4 var(--f-ui);color:var(--ink-3);margin-top:3px">DM 7 — Suites…</span>
                </div>
              </div>
            </div>
            <div style="height:48px;border-top:.5px solid var(--line);display:flex;align-items:center;justify-content:space-around;padding:0 16px">
              @foreach([
                '<path d="M3 10.5L10 4L17 10.5V16a1 1 0 01-1 1h-3v-5h-4v5H4a1 1 0 01-1-1z"/>',
                '<circle cx="9" cy="9" r="5"/><path d="m13 13 4 4"/>',
                '<path d="M10 4v12M4 10h12"/>',
                '<path d="M5 14L5 9a5 5 0 0 1 10 0v5l1.5 2H3.5zM8 17a2 2 0 0 0 4 0"/>',
                '<circle cx="8" cy="7" r="3"/><path d="M2 16a4 4 0 0 1 12 0"/>',
              ] as $i => $path)
              <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="{{ $i===0?'var(--ink)':'var(--ink-3)' }}" stroke-width="1.4" stroke-linecap="round">{!! $path !!}</svg>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ════════ MARQUEE ════════ --}}
<section style="padding:32px 0;border-top:.5px solid var(--line);border-bottom:.5px solid var(--line);background:var(--surface)">
  <div style="text-align:center;margin-bottom:22px">
    <span class="lp-eyebrow">Présent dans 142 écoles à travers le Royaume</span>
  </div>
  <div class="lp-marquee-wrap">
    <div class="lp-marquee-track">
      @php $schools = ['Lycée Al Kindi','Collège Ibn Rushd','Lycée Averroès','Institut Atlas','Lycée Lyautey','Collège Ibn Sina','École Mohamed VI','Lycée Lalla Aïcha','Collège Averroès','Lycée Descartes','Institut Al-Andalous','École Anfa']; @endphp
      @foreach(array_merge($schools,$schools) as $i => $school)
      <span class="lp-display" style="font-size:22px;color:{{ $i%5===2?'var(--c-blue)':'var(--ink-3)' }}">{{ $school }}</span>
      @endforeach
    </div>
  </div>
</section>

{{-- ════════ MANIFESTO ════════ --}}
<section id="manifeste" class="lp-sec" style="padding-top:130px;padding-bottom:100px">
  <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:80px">
    <div>
      <div class="lp-eyebrow">02 — Manifeste</div>
      <h2 class="lp-display" style="font-size:clamp(40px,5vw,64px);margin:20px 0 0">
        L'école mérite un <span class="mj-grad-text">espace numérique</span> à sa hauteur.
      </h2>
    </div>
    <div style="padding-top:60px;font:400 17px/1.65 var(--f-ui);color:var(--ink-2)">
      <p style="margin-top:0">
        Les outils que nos écoles utilisent — WhatsApp, Google Classroom, MySchool — ne sont pas <em>les nôtres</em>. Ils sont pensés ailleurs, pour d'autres rythmes, dans d'autres langues.
      </p>
      <p>
        Majakker est conçu à Rabat, pour la vie scolaire marocaine. Bilingue. Calme. Sans algorithme de doomscroll. Avec une modération IA qui comprend la darija et les codes de la classe.
      </p>
      <p style="margin-bottom:0;color:var(--ink)">
        <strong style="font-weight:600">Hébergé à Rabat · Conforme à la loi 09-08 sur les données personnelles.</strong>
      </p>
    </div>
  </div>
</section>

{{-- ════════ BENTO FEATURES ════════ --}}
<section id="bento" class="lp-sec" style="padding-bottom:120px">
  <div class="lp-eyebrow" style="margin-bottom:22px">03 — Pourquoi Majakker</div>
  <h2 class="lp-display" style="font-size:clamp(40px,4.6vw,60px);margin:0 0 50px;max-width:840px">
    Une plateforme pensée<br>pour <span class="mj-grad-text">apprendre</span>, pas pour <span class="mj-grad-text">scroller</span>.
  </h2>

  <div class="lp-bento">

    {{-- Calme — 7 col × 2 row --}}
    <div class="lp-card" style="grid-column:span 7;grid-row:span 2">
      <div class="lp-icon-tile" style="background:var(--c-blue-soft);color:#2A3FB8">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M16 11A6 6 0 1 1 9 4a5 5 0 0 0 7 7z"/></svg>
      </div>
      <h3 class="lp-display" style="font-size:38px;margin:22px 0 12px">Calme par défaut</h3>
      <p style="font:400 14.5px/1.6 var(--f-ui);color:var(--ink-2);margin:0;max-width:460px">
        Pas d'algorithme à scroller à l'infini. Le fil suit la cadence de l'école : annonces, classes, clubs. Rien de plus. Pas de like-bait, pas de notifications fantômes.
      </p>
      <div style="margin-top:auto;padding-top:28px;display:flex;gap:4px;align-items:flex-end;height:110px">
        @foreach([20,28,42,36,54,48,62,56,72,68,84,78,92] as $i => $v)
        <div style="flex:1;height:{{ $v }}%;border-radius:4px 4px 1px 1px;
          background:{{ $i===12?'var(--c-blue)':($i>=9?'color-mix(in oklab,var(--c-blue) 50%,var(--surface-2))':'var(--surface-2)') }}"></div>
        @endforeach
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:8px;font:400 9.5px/1 var(--f-mono);color:var(--ink-4);letter-spacing:.08em">
        <span>S08</span><span>S14</span><span>S20</span>
      </div>
    </div>

    {{-- Pédagogique — 5 col --}}
    <div class="lp-card" style="grid-column:span 5">
      <div class="lp-icon-tile" style="background:var(--c-saffron-soft);color:#8A6520">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M4 5a3 1.5 0 0 1 8 0a3 1.5 0 0 1 8 0v12a3 1.5 0 0 0-8 0a3 1.5 0 0 0-8 0zM12 5v12"/></svg>
      </div>
      <h3 class="lp-display" style="font-size:28px;margin:18px 0 8px">Pédagogique d'abord</h3>
      <p style="font:400 13px/1.55 var(--f-ui);color:var(--ink-2);margin:0">
        Devoirs, polycopiés, sondages, fichiers de classe. Tous les outils dans un seul fil. Sans plug-in, sans Drive externe.
      </p>
    </div>

    {{-- Modération IA — 5 col --}}
    <div class="lp-card" style="grid-column:span 5">
      <div class="lp-icon-tile" style="background:var(--c-atlas-soft);color:#2D6B61">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M10 3L17 6V12a7 7 0 0 1-7 6a7 7 0 0 1-7-6V6z"/><path d="M7 10l2 2 4-4"/></svg>
      </div>
      <h3 class="lp-display" style="font-size:28px;margin:18px 0 8px">Modération IA bilingue</h3>
      <p style="font:400 13px/1.55 var(--f-ui);color:var(--ink-2);margin:0">
        Une IA entraînée sur l'arabe marocain. Comprend les nuances, repère les abus, alerte la direction sans bloquer la classe.
      </p>
    </div>

    {{-- Engagement dark — 4 col --}}
    <div class="lp-card lp-card-dark" style="grid-column:span 4">
      <div class="lp-eyebrow on-dark">Engagement</div>
      <div class="lp-display" style="font-size:88px;margin:10px 0 0;letter-spacing:-.04em">
        72<span style="font-size:32px;opacity:.55">%</span>
      </div>
      <div style="font:400 12.5px/1.5 var(--f-ui);color:rgba(250,247,242,.65);margin-top:8px">
        des élèves participent au moins une fois par semaine.
      </div>
      <div style="margin-top:auto;padding-top:18px;display:flex;align-items:center;gap:6px;font:500 10.5px/1 var(--f-mono);color:rgba(250,247,242,.45);letter-spacing:.08em">
        <span style="width:6px;height:6px;border-radius:999px;background:var(--c-atlas)"></span>
        +4 PTS vs S19
      </div>
    </div>

    {{-- Quatre rôles — 8 col --}}
    <div class="lp-card" style="grid-column:span 8">
      <div class="lp-eyebrow">Quatre rôles · une seule plateforme</div>
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:16px">
        @foreach([['Étudiant','Yasmine B.','blue'],['Professeur','K. El Idrissi','atlas'],['Directeur','N. Tazi','saffron'],['Admin','R. Amrani','terracotta']] as [$role,$name,$tone])
        @php
          $t2=[['#F4EFE6','#5C5346'],['#E8ECFF','#2A3FB8'],['#FAF1DD','#8A6520'],['#F8E7DD','#8E4A2E'],['#DEEFEC','#2D6B61'],['#ECE4D6','#3E342A']];
          $h=0;foreach(str_split($name) as $ch){$h=(($h*31)+ord($ch))&0x7FFFFFFF;}
          [$abg,$afg]=$t2[$h%6];
          $ai=collect(explode(' ',$name))->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))->take(2)->implode('');
        @endphp
        <div style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--c-{{ $tone }}-soft);border-radius:14px;position:relative;overflow:hidden">
          <div style="position:absolute;top:-10px;right:-10px;color:var(--c-{{ $tone }});opacity:.22;pointer-events:none">
            <svg width="48" height="48" viewBox="0 0 64 64" style="opacity:1"><g fill="none" stroke="currentColor" stroke-width="0.8"><path d="M32 6L40 22L56 22L44 34L48 50L32 42L16 50L20 34L8 22L24 22Z"/><circle cx="32" cy="28" r="4"/></g></svg>
          </div>
          <div class="lp-av" style="width:32px;height:32px;font-size:11px;border-radius:9px;background:{{ $abg }};color:{{ $afg }}">{{ $ai }}</div>
          <div style="position:relative">
            <div style="font:500 11.5px/1.2 var(--f-ui);color:var(--c-{{ $tone }})">{{ $role }}</div>
            <div style="font:400 10.5px/1.2 var(--f-ui);color:var(--ink-2);margin-top:3px">{{ $name }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div>
</section>

{{-- ════════ STATS BAND ════════ --}}
<section id="stats" class="lp-sec" style="padding-bottom:120px">
  <div class="lp-stats-band">
    <div style="position:relative;z-index:1">
      <div class="lp-eyebrow on-dark">04 — En chiffres</div>
      <h2 class="lp-display" style="font-size:clamp(40px,4.6vw,60px);margin:20px 0 56px;color:var(--bg);max-width:740px">
        L'année scolaire <span style="background:var(--mj-gradient);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic">2025–26</span>,<br>en quatre nombres.
      </h2>
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px">
        @foreach([['86 134','élèves actifs ce mois'],['142','écoles partenaires au Maroc'],['1,2 M','publications depuis la rentrée'],['0,4 %','contenus signalés par notre IA']] as [$n,$l])
        <div>
          <div class="lp-display" style="font-size:clamp(40px,4.8vw,64px);color:var(--bg);letter-spacing:-.04em">{{ $n }}</div>
          <div style="font:400 12.5px/1.5 var(--f-ui);color:rgba(250,247,242,.65);margin-top:10px;max-width:200px">{{ $l }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- ════════ TESTIMONIAL ════════ --}}
<section class="lp-sec" style="padding-bottom:130px">
  <div class="lp-eyebrow">05 — Voix de l'école</div>
  <div style="position:relative;margin-top:32px;padding:30px 0 0 80px;max-width:1020px">
    <span class="lp-qmark">«</span>
    <p class="lp-display" style="font-size:clamp(28px,3.4vw,46px);line-height:1.18;margin:0;font-style:italic">
      Majakker a remplacé six groupes WhatsApp, deux Drives et une boîte à idées en carton.
      Pour la première fois, l'école parle d'<span class="mj-grad-text">une seule voix</span>.
    </p>
    <div style="display:flex;align-items:center;gap:14px;margin-top:34px">
      <div class="lp-av" style="width:46px;height:46px;font-size:15px;border-radius:12px;background:#FAF1DD;color:#8A6520;flex-shrink:0">NT</div>
      <div>
        <div style="font:500 13.5px/1.3 var(--f-ui);color:var(--ink)">Najat Tazi</div>
        <div style="font:400 12px/1.3 var(--f-ui);color:var(--ink-3)">Directrice — Lycée Majakker, Casablanca</div>
      </div>
    </div>
  </div>
</section>

{{-- ════════ PROCESS ════════ --}}
<section id="process" class="lp-sec" style="padding-bottom:130px">
  <div class="lp-eyebrow">06 — Démarrer</div>
  <h2 class="lp-display" style="font-size:clamp(40px,4.6vw,60px);margin:20px 0 60px;max-width:820px">
    De votre première démo<br>à la rentrée, en <span class="mj-grad-text">30 jours</span>.
  </h2>
  <div class="lp-proc">
    @foreach([
      ['01','Démo','Une session de 30 minutes avec un membre de notre équipe à Rabat. Aucune carte bancaire requise.'],
      ['02','Migration','Nous importons vos classes, vos enseignants et vos contenus depuis WhatsApp, Drive ou MySchool.'],
      ['03','Rentrée','Formation des équipes en présentiel. Support continu pendant les six premiers mois — sans surcoût.'],
    ] as [$n,$t,$d])
    <div style="position:relative;z-index:1;background:var(--bg);padding:0 12px">
      <div style="width:56px;height:56px;border-radius:999px;background:var(--ink);color:var(--bg);
                  display:flex;align-items:center;justify-content:center;margin-bottom:20px;
                  font-family:var(--f-display);font-size:22px;font-style:italic">{{ $n }}</div>
      <h3 class="lp-display" style="font-size:30px;margin:0 0 10px">{{ $t }}</h3>
      <p style="font:400 14px/1.6 var(--f-ui);color:var(--ink-2);margin:0">{{ $d }}</p>
    </div>
    @endforeach
  </div>
</section>

{{-- ════════ FINAL CTA ════════ --}}
<section class="lp-sec" style="padding-bottom:80px">
  <div style="position:relative;background:var(--mj-ink);color:var(--bg);border-radius:28px;padding:84px 64px;overflow:hidden">
    <div class="mj-ring"></div>
    {{-- Orb --}}
    <div style="position:absolute;top:-120px;right:-120px;width:460px;height:460px;
                background:var(--mj-gradient-strong);border-radius:50%;
                filter:blur(60px);opacity:.6;pointer-events:none"></div>
    {{-- Grid --}}
    <div style="position:absolute;inset:0;opacity:.06;pointer-events:none;
                background-image:linear-gradient(rgba(255,255,255,.7) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.7) 1px,transparent 1px);
                background-size:44px 44px;
                -webkit-mask-image:radial-gradient(ellipse at center,#000 30%,transparent 75%);
                mask-image:radial-gradient(ellipse at center,#000 30%,transparent 75%)"></div>
    <div style="position:relative;display:grid;grid-template-columns:1.4fr 1fr;gap:56px;align-items:center">
      <div>
        <div class="lp-eyebrow on-dark">Pour les directions</div>
        <h2 class="lp-display" style="font-size:clamp(40px,5vw,64px);margin:20px 0 22px;letter-spacing:-.03em">
          Donnez à votre école<br>
          un <span style="background:linear-gradient(135deg,#C7B6FF 0%,#93C5FD 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic">espace numérique</span><br>
          digne d'elle.
        </h2>
        <p style="font:400 15px/1.65 var(--f-ui);color:rgba(250,247,242,.72);margin:0;max-width:520px">
          Aucune carte bancaire. Migration accompagnée depuis WhatsApp, Google Classroom et MySchool. Démo en 30 minutes avec un membre de notre équipe à Rabat.
        </p>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <a href="{{ route('contact') }}" class="lp-cta lp-cta-light" style="height:52px;font-size:14px;justify-content:center">
          Réserver une démo
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M5 10h10M12 6l4 4-4 4"/></svg>
        </a>
        <button class="lp-cta lp-cta-ghost" style="border-color:rgba(250,247,242,.25);color:var(--bg);height:52px;justify-content:center">
          Lire le manifeste
        </button>
        <div style="font:400 11px/1.5 var(--f-mono);color:rgba(250,247,242,.5);margin-top:8px;text-align:center;letter-spacing:.06em">
          RÉPONSE SOUS 24 H · CONTACT@MAJAKKER.MA
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ════════ FOOTER ════════ --}}
<footer class="lp-sec" style="padding-top:60px;padding-bottom:40px;border-top:.5px solid var(--line)">
  <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:40px;margin-bottom:50px">
    <div>
      <a href="/" style="display:inline-flex">
        <img src="{{ asset('images/logo.png') }}" alt="Majakker" style="height:56px;width:auto;display:block"/>
      </a>
      <p style="font:400 13px/1.6 var(--f-ui);color:var(--ink-3);max-width:280px;margin:14px 0 0">
        La place publique numérique des écoles marocaines.<br>
        Conçu à Rabat · Hébergé au Maroc.
      </p>
    </div>
    @foreach([
      ['Plateforme', ['Fil','Classes','Modération','Tableau de bord','Mobile']],
      ['Écoles',     ['Démo','Migration','Tarifs','Cas client']],
      ['Entreprise', ['Manifeste','Équipe','Carrières','Presse']],
      ['Légal',      ['Confidentialité','Loi 09-08','Charte','Contact']],
    ] as [$title,$links])
    <div>
      <div class="lp-eyebrow" style="margin-bottom:16px">{{ $title }}</div>
      <div style="display:flex;flex-direction:column;gap:10px">
        @foreach($links as $link)
        <a href="#" style="font:400 13px/1 var(--f-ui);color:var(--ink-2);text-decoration:none;position:relative;display:inline-block;width:fit-content"
           onmouseenter="this.style.color='var(--ink)'" onmouseleave="this.style.color='var(--ink-2)'">{{ $link }}</a>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>
  <div style="display:flex;align-items:center;gap:16px;padding-top:24px;border-top:.5px solid var(--line)">
    <span style="font:400 11px/1 var(--f-mono);letter-spacing:.08em;color:var(--ink-3)">MAJAKKER © {{ date('Y') }} — RABAT, MAROC</span>
    <span style="flex:1"></span>
    <span style="font:400 11px/1 var(--f-mono);letter-spacing:.08em;color:var(--ink-4)">v.{{ date('Y') }}.{{ date('m') }} · BETA</span>
  </div>
</footer>

</div>{{-- /.lp-root --}}

<script>
// Cursor glow tracking
(function(){
  const hero = document.getElementById('hero-section');
  if (!hero) return;
  hero.addEventListener('mousemove', e => {
    const r = hero.getBoundingClientRect();
    hero.style.setProperty('--mx', (e.clientX - r.left) + 'px');
    hero.style.setProperty('--my', (e.clientY - r.top) + 'px');
  });
})();
</script>
</x-layouts.guest>
